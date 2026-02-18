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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('business_id'); // Using business_id instead of hospital_id
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->enum('balance_type', ['Dr', 'Cr'])->default('Dr');
            $table->string('contact')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['active', 'inactive', 'default'])->default('active');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');

            // Indexes for better performance
            $table->index('business_id');
            $table->index('status');
            $table->index(['business_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
