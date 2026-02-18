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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->unsignedBigInteger('business_id');
            // transaction_type: Payment, Receipt, Journal, or Contra
            $table->enum('transaction_type', ['Payment', 'Receipt', 'Journal', 'Contra']);
            $table->date('transaction_date');
            // The total amount of the transaction if needed
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('narration')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');

            // Indexes for better performance
            $table->index('business_id');
            $table->index('transaction_type');
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First drop all dependent tables
        Schema::dropIfExists('transaction_lines');
        // Finally drop the transactions table
        Schema::dropIfExists('transactions');
    }
};
