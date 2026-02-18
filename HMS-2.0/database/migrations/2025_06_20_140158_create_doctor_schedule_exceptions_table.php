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
        Schema::create('doctor_schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('date')->nullable(); // null for recurring exceptions
            $table->time('start_time')->nullable(); // null for full day
            $table->time('end_time')->nullable(); // null for full day
            $table->boolean('is_available')->default(false); // false = unavailable, true = extra availability
            $table->string('reason')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_pattern')->nullable(); // for recurring patterns
            $table->timestamps();

            // Indexes
            $table->index(['doctor_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedule_exceptions');
    }
};
