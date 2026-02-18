<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalInvoiceLinesTable extends Migration
{
    public function up()
    {
        Schema::create('medical_invoice_lines', function (Blueprint $table) {
            $table->id();
            // Link to the invoice header record
            $table->foreignId('medical_invoice_id')->constrained('medical_invoices')->onDelete('cascade');
            // The test involved in this line
            $table->foreignId('lab_test_id')->constrained('lab_tests')->onDelete('cascade');
            // Quantity of tests (usually 1 for medical tests)
            $table->decimal('quantity', 10, 2)->default(1);
            // The price per test at which the test is charged
            $table->decimal('unit_price', 10, 2);
            // (Optional) discount applied on this line
            $table->decimal('line_discount', 10, 2)->nullable()->default(0);
            // The total amount for the line (calculated as quantity * unit_price - line_discount)
            $table->decimal('line_total', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_invoice_lines');
    }
}
