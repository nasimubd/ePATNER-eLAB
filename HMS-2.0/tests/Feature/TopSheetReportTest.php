<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Business;
use App\Models\MedicalInvoice;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class TopSheetReportTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $business;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a business
        $this->business = Business::factory()->create([
            'is_active' => true,
            'hospital_name' => 'Test Hospital'
        ]);

        // Create a user with admin role
        $this->user = User::factory()->create([
            'business_id' => $this->business->id
        ]);

        // Assign admin role if role management exists
        try {
            if (method_exists($this->user, 'assignRole')) {
                $this->user->assignRole('admin');
            }
        } catch (\Exception $e) {
            // Role management not available, continue without it
        }
    }

    /** @test */
    public function it_can_access_top_sheet_report_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.reports.top-sheet.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.top-sheet.index');
        $response->assertViewHas(['startDate', 'endDate', 'reportData', 'business']);
    }

    /** @test */
    public function it_can_get_top_sheet_data_via_api()
    {
        // Create some test data
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

        // Create a medical invoice
        MedicalInvoice::factory()->create([
            'business_id' => $this->business->id,
            'invoice_date' => Carbon::now(),
            'grand_total' => 1000,
            'paid_amount' => 800,
            'status' => 'paid'
        ]);

        // Create a transaction (expense)
        Transaction::factory()->create([
            'business_id' => $this->business->id,
            'transaction_date' => Carbon::now(),
            'transaction_type' => 'Payment',
            'amount' => 200
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('admin.reports.top-sheet.data', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'breakdown' => [
                '*' => [
                    'date',
                    'date_formatted',
                    'sales',
                    'collected_sales',
                    'expenses',
                    'commission',
                    'net_profit'
                ]
            ],
            'totals' => [
                'sales',
                'collected_sales',
                'expenses',
                'commission',
                'net_profit'
            ],
            'date_range' => [
                'start',
                'end'
            ]
        ]);
    }

    /** @test */
    public function it_validates_date_range_in_api_request()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('admin.reports.top-sheet.data', [
                'start_date' => '2025-01-01',
                'end_date' => '2024-12-31' // End date before start date
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_date']);
    }

    /** @test */
    public function it_rejects_date_range_exceeding_365_days()
    {
        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays(366)->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->getJson(route('admin.reports.top-sheet.data', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]));

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Date range cannot exceed 365 days']);
    }

    /** @test */
    public function it_can_access_print_view()
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');

        $response = $this->actingAs($this->user)
            ->get(route('admin.reports.top-sheet.print', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]));

        $response->assertStatus(200);
        // The view will be either print-single or print-multi depending on data size
        $this->assertTrue(
            $response->viewIs('admin.reports.top-sheet.print-single') ||
                $response->viewIs('admin.reports.top-sheet.print-multi')
        );
        $response->assertViewHas(['startDate', 'endDate', 'reportData', 'business']);
    }

    /** @test */
    public function it_calculates_totals_correctly()
    {
        $today = Carbon::now();

        // Create multiple invoices
        MedicalInvoice::factory()->create([
            'business_id' => $this->business->id,
            'invoice_date' => $today,
            'grand_total' => 1000,
            'paid_amount' => 800,
            'status' => 'paid'
        ]);

        MedicalInvoice::factory()->create([
            'business_id' => $this->business->id,
            'invoice_date' => $today,
            'grand_total' => 500,
            'paid_amount' => 500,
            'status' => 'paid'
        ]);

        // Create expenses
        Transaction::factory()->create([
            'business_id' => $this->business->id,
            'transaction_date' => $today,
            'transaction_type' => 'Payment',
            'amount' => 200
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('admin.reports.top-sheet.data', [
                'start_date' => $today->format('Y-m-d'),
                'end_date' => $today->format('Y-m-d')
            ]));

        $response->assertStatus(200);

        $data = $response->json();

        // Check totals
        $this->assertEquals(1500, $data['totals']['sales']); // 1000 + 500
        $this->assertEquals(1300, $data['totals']['collected_sales']); // 800 + 500
        $this->assertEquals(200, $data['totals']['expenses']);
        $this->assertEquals(1100, $data['totals']['net_profit']); // 1300 - 200 - 0 (no commission)
    }
}
