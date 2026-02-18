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
        Schema::create('letterheads', function (Blueprint $table) {
            $table->id();
            $table->string('business_name_bangla');
            $table->string('business_name_english');
            $table->text('location');
            $table->json('contacts')->nullable(); // Multiple contacts allowed
            $table->json('emails')->nullable(); // Multiple emails allowed
            $table->enum('type', ['Invoice', 'Lab Report']);
            $table->enum('status', ['Active', 'Inactive'])->default('Inactive');
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['business_id', 'type']);
            $table->index(['business_id', 'type', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letterheads');
    }
};
