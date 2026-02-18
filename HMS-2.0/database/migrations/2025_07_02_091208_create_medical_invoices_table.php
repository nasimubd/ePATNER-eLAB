<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('medical_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            // Reference to the patient
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->dateTime('invoice_date');
            // Payment method: cash or credit
            $table->enum('payment_method', ['cash', 'credit']);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount', 15, 2)->nullable()->default(0);
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('payment_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_invoices');
    }
}
