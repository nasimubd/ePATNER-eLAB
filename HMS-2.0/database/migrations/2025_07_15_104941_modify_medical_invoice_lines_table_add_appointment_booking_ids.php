<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_invoice_lines', function (Blueprint $table) {
            // Make lab_test_id nullable
            $table->foreignId('lab_test_id')->nullable()->change();

            // Add appointment_id and booking_id as nullable foreign keys
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('cascade');

            // Add service_type to distinguish between different line types
            $table->enum('service_type', ['lab_test', 'consultation', 'booking', 'commission'])->default('lab_test');

            // Add service_name for better identification
            $table->string('service_name')->nullable();

            // Add indexes for better performance
            $table->index(['appointment_id']);
            $table->index(['booking_id']);
            $table->index(['service_type']);
        });
    }

    public function down(): void
    {
        Schema::table('medical_invoice_lines', function (Blueprint $table) {
            // Remove the new columns
            $table->dropForeign(['appointment_id']);
            $table->dropForeign(['booking_id']);
            $table->dropColumn(['appointment_id', 'booking_id', 'service_type', 'service_name']);

            // Make lab_test_id required again
            $table->foreignId('lab_test_id')->nullable(false)->change();
        });
    }
};
