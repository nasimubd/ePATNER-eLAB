<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'medicine_db';

    public function up(): void
    {
        Schema::connection('medicine_db')->create('common_medicines', function (Blueprint $table) {
            $table->id();

            // Unique Medicine ID - Human readable format
            $table->string('medicine_id', 20)->unique()->comment('Unique medicine identifier (e.g., MED-2024-000001)');

            $table->string('company_name', 100);
            $table->string('dosage_form', 50);
            $table->string('brand_name', 150);
            $table->string('generic_name', 200);
            $table->string('dosage_strength', 100);
            $table->string('pack_info', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Primary search indexes for lightning-fast search
            $table->index(['medicine_id', 'is_active'], 'idx_medicine_id_active');
            $table->index(['brand_name', 'is_active'], 'idx_brand_active');
            $table->index(['generic_name', 'is_active'], 'idx_generic_active');
            $table->index(['company_name', 'is_active'], 'idx_company_active');
            $table->index(['dosage_form', 'is_active'], 'idx_dosage_form_active');

            // Composite indexes for complex searches
            $table->index(['brand_name', 'generic_name', 'is_active'], 'idx_brand_generic_active');
            $table->index(['company_name', 'brand_name', 'is_active'], 'idx_company_brand_active');

            // Full-text search index for advanced search
            $table->fullText(['brand_name', 'generic_name', 'company_name'], 'idx_fulltext_search');
        });
    }

    public function down(): void
    {
        Schema::connection('medicine_db')->dropIfExists('common_medicines');
    }
};
