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
        Schema::create('care_ofs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('phone_number');
            $table->decimal('commission_rate', 5, 2)->default(0.00); // Commission percentage (e.g., 5.50 for 5.5%)
            $table->enum('commission_type', ['fixed'])->default('fixed');
            $table->decimal('fixed_commission_amount', 10, 2)->nullable(); // For fixed commission
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('ledger_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('ledger_id')->references('id')->on('ledgers')->onDelete('set null');

            $table->index(['business_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('care_ofs');
    }
};
