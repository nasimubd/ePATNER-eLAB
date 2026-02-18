<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('medicine_type')->default('tablet'); // tablet, capsule, syrup, injection, etc.
            $table->string('strength')->nullable(); // e.g., 500mg, 10ml
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->integer('minimum_stock_level')->default(10);
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('storage_conditions')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('dosage_instructions')->nullable();
            $table->boolean('prescription_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('barcode')->nullable()->unique();
            $table->string('medicine_image')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'name']);
            $table->index(['business_id', 'is_active']);
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
