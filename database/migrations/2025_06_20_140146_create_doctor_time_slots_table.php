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
        Schema::create('doctor_time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('date');
            $table->time('time_slot');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_booked')->default(false);
            $table->enum('slot_type', ['regular', 'emergency', 'blocked'])->default('regular');
            $table->integer('max_appointments')->default(1);
            $table->integer('current_appointments')->default(0);
            $table->timestamps();

            // Unique constraint to prevent duplicate slots
            $table->unique(['doctor_id', 'date', 'time_slot']);

            // Indexes
            $table->index(['doctor_id', 'date']);
            $table->index(['date', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_time_slots');
    }
};
