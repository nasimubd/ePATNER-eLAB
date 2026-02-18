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
        Schema::table('medical_invoices', function (Blueprint $table) {
            // Add composite index for business_id and invoice_date for faster filtering
            $table->index(['business_id', 'invoice_date'], 'idx_medical_invoices_business_date');

            // Add index for status filtering
            $table->index(['status'], 'idx_medical_invoices_status');
        });

        Schema::table('transactions', function (Blueprint $table) {
            // Add composite index for business_id and transaction_date for faster filtering
            $table->index(['business_id', 'transaction_date'], 'idx_transactions_business_date');

            // Add index for transaction_type filtering
            $table->index(['transaction_type'], 'idx_transactions_type');
        });

        Schema::table('medical_invoice_lines', function (Blueprint $table) {
            // Add index for medical_invoice_id for faster joins
            $table->index(['medical_invoice_id'], 'idx_medical_invoice_lines_invoice_id');

            // Add index for service_type filtering (for commission lines)
            $table->index(['service_type'], 'idx_medical_invoice_lines_service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_invoices', function (Blueprint $table) {
            $table->dropIndex('idx_medical_invoices_business_date');
            $table->dropIndex('idx_medical_invoices_status');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_business_date');
            $table->dropIndex('idx_transactions_type');
        });

        Schema::table('medical_invoice_lines', function (Blueprint $table) {
            $table->dropIndex('idx_medical_invoice_lines_invoice_id');
            $table->dropIndex('idx_medical_invoice_lines_service_type');
        });
    }
};
