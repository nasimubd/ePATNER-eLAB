<?php

namespace App\Services;

use App\Models\CommonMedicine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class CommonMedicineImportService
{
    private const BATCH_SIZE = 1000;

    /**
     * Import medicines from CSV file
     */
    public function importFromCsv(string $filePath, bool $hasMedicineId = false): array
    {
        try {
            $data = Excel::toCollection(null, $filePath)->first();

            // Remove header row if exists
            if ($this->hasHeaderRow($data->first())) {
                $data = $data->skip(1);
            }

            return $this->processBatchImport($data, $hasMedicineId);
        } catch (Exception $e) {
            Log::error('Common medicine import failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Import medicines from array
     */
    public function importFromArray(array $medicines, bool $hasMedicineId = false): array
    {
        $collection = collect($medicines);
        return $this->processBatchImport($collection, $hasMedicineId);
    }

    /**
     * Process batch import with transaction
     */
    private function processBatchImport(Collection $data, bool $hasMedicineId = false): array
    {
        $totalRecords = $data->count();
        $imported = 0;
        $failed = 0;
        $errors = [];
        $duplicates = 0;

        DB::connection('medicine_db')->beginTransaction();

        try {
            $data->chunk(self::BATCH_SIZE)->each(function ($chunk) use (&$imported, &$failed, &$errors, &$duplicates, $hasMedicineId) {
                $batchData = [];

                foreach ($chunk as $index => $row) {
                    $medicineData = $this->validateAndFormatRow($row, $hasMedicineId, $index);

                    if ($medicineData['valid']) {
                        // Check for duplicates
                        if ($this->isDuplicate($medicineData['data'])) {
                            $duplicates++;
                            continue;
                        }

                        $batchData[] = $medicineData['data'];
                    } else {
                        $failed++;
                        $errors[] = $medicineData['error'];
                    }
                }

                if (!empty($batchData)) {
                    // Generate medicine IDs for batch insert
                    foreach ($batchData as &$medicine) {
                        if (empty($medicine['medicine_id'])) {
                            $medicine['medicine_id'] = $this->generateUniqueMedicineId();
                        }
                    }

                    CommonMedicine::insert($batchData);
                    $imported += count($batchData);
                }
            });

            DB::connection('medicine_db')->commit();

            return [
                'success' => true,
                'total' => $totalRecords,
                'imported' => $imported,
                'failed' => $failed,
                'duplicates' => $duplicates,
                'errors' => array_slice($errors, 0, 100) // Limit errors to first 100
            ];
        } catch (Exception $e) {
            DB::connection('medicine_db')->rollBack();
            throw $e;
        }
    }

    /**
     * Validate and format row data
     */
    private function validateAndFormatRow($row, bool $hasMedicineId = false, int $index = 0): array
    {
        try {
            // Handle both array and object formats
            $data = is_array($row) ? $row : $row->toArray();

            // Map CSV columns based on whether medicine_id is included
            if ($hasMedicineId) {
                // Format: [Medicine ID][Company Name][Dosage Form][Brand Name][Generic Name][Dosage/Strength][Pack Info]
                $medicineData = [
                    'medicine_id' => $this->cleanString($data[0] ?? ''),
                    'company_name' => $this->cleanString($data[1] ?? ''),
                    'dosage_form' => $this->cleanString($data[2] ?? ''),
                    'brand_name' => $this->cleanString($data[3] ?? ''),
                    'generic_name' => $this->cleanString($data[4] ?? ''),
                    'dosage_strength' => $this->cleanString($data[5] ?? ''),
                    'pack_info' => $this->cleanString($data[6] ?? ''),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                // Format: [Company Name][Dosage Form][Brand Name][Generic Name][Dosage/Strength][Pack Info]
                $medicineData = [
                    'medicine_id' => '', // Will be auto-generated
                    'company_name' => $this->cleanString($data[0] ?? ''),
                    'dosage_form' => $this->cleanString($data[1] ?? ''),
                    'brand_name' => $this->cleanString($data[2] ?? ''),
                    'generic_name' => $this->cleanString($data[3] ?? ''),
                    'dosage_strength' => $this->cleanString($data[4] ?? ''),
                    'pack_info' => $this->cleanString($data[5] ?? ''),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Validation
            $validationErrors = $this->validateMedicineData($medicineData, $index);
            if (!empty($validationErrors)) {
                return [
                    'valid' => false,
                    'error' => "Row " . ($index + 1) . ": " . implode(', ', $validationErrors)
                ];
            }

            // Check for duplicate medicine_id if provided
            if ($hasMedicineId && !empty($medicineData['medicine_id'])) {
                $exists = CommonMedicine::where('medicine_id', $medicineData['medicine_id'])->exists();
                if ($exists) {
                    return [
                        'valid' => false,
                        'error' => "Row " . ($index + 1) . ": Medicine ID already exists: " . $medicineData['medicine_id']
                    ];
                }
            }

            return [
                'valid' => true,
                'data' => $medicineData
            ];
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => "Row " . ($index + 1) . ": Invalid row format - " . $e->getMessage()
            ];
        }
    }

    /**
     * Validate medicine data
     */
    private function validateMedicineData(array $data, int $index): array
    {
        $errors = [];

        if (empty($data['brand_name'])) {
            $errors[] = 'Brand name is required';
        }

        if (empty($data['generic_name'])) {
            $errors[] = 'Generic name is required';
        }

        if (empty($data['company_name'])) {
            $errors[] = 'Company name is required';
        }

        if (empty($data['dosage_form'])) {
            $errors[] = 'Dosage form is required';
        }

        // Check field lengths
        if (strlen($data['company_name']) > 100) {
            $errors[] = 'Company name too long (max 100 characters)';
        }

        if (strlen($data['dosage_form']) > 50) {
            $errors[] = 'Dosage form too long (max 50 characters)';
        }

        if (strlen($data['brand_name']) > 150) {
            $errors[] = 'Brand name too long (max 150 characters)';
        }

        if (strlen($data['generic_name']) > 200) {
            $errors[] = 'Generic name too long (max 200 characters)';
        }

        if (strlen($data['dosage_strength']) > 100) {
            $errors[] = 'Dosage strength too long (max 100 characters)';
        }

        if (strlen($data['pack_info']) > 100) {
            $errors[] = 'Pack info too long (max 100 characters)';
        }

        return $errors;
    }

    /**
     * Check if medicine is duplicate
     */
    private function isDuplicate(array $medicineData): bool
    {
        return CommonMedicine::where('brand_name', $medicineData['brand_name'])
            ->where('generic_name', $medicineData['generic_name'])
            ->where('company_name', $medicineData['company_name'])
            ->where('dosage_strength', $medicineData['dosage_strength'])
            ->exists();
    }

    /**
     * Check if first row is header
     */
    private function hasHeaderRow($firstRow): bool
    {
        if (!$firstRow) return false;

        $data = is_array($firstRow) ? $firstRow : $firstRow->toArray();
        $firstCell = strtolower(trim($data[0] ?? ''));

        // Common header indicators
        $headerIndicators = ['company', 'medicine', 'brand', 'generic', 'dosage', 'pack'];

        foreach ($headerIndicators as $indicator) {
            if (str_contains($firstCell, $indicator)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clean and trim string data
     */
    private function cleanString(?string $value): string
    {
        if ($value === null) return '';

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    /**
     * Generate unique medicine ID for batch insert
     */
    private function generateUniqueMedicineId(): string
    {
        $year = date('Y');
        $prefix = "MED-{$year}-";

        // Get the last medicine ID for current year
        $lastMedicine = CommonMedicine::where('medicine_id', 'LIKE', $prefix . '%')
            ->orderBy('medicine_id', 'desc')
            ->first();

        if ($lastMedicine) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastMedicine->medicine_id, -6);
            $newNumber = $lastNumber + 1;
        } else {
            // First medicine of the year
            $newNumber = 1;
        }

        // Format with leading zeros (6 digits)
        $formattedNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        return $prefix . $formattedNumber;
    }

    /**
     * Get sample CSV format
     */
    public function getSampleCsvFormat(bool $includeMedicineId = false): array
    {
        if ($includeMedicineId) {
            return [
                'headers' => ['Medicine ID', 'Company Name', 'Dosage Form', 'Brand Name', 'Generic Name', 'Dosage/Strength', 'Pack Info'],
                'sample' => ['MED-2024-000001', 'ABC Pharma', 'Tablet', 'Panadol', 'Paracetamol', '500mg', '10 tablets']
            ];
        } else {
            return [
                'headers' => ['Company Name', 'Dosage Form', 'Brand Name', 'Generic Name', 'Dosage/Strength', 'Pack Info'],
                'sample' => ['ABC Pharma', 'Tablet', 'Panadol', 'Paracetamol', '500mg', '10 tablets']
            ];
        }
    }
}
