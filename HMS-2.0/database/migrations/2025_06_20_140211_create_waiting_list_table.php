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
        Schema::create('waiting_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->date('preferred_date_start');
            $table->date('preferred_date_end');
            $table->time('preferred_time_start')->nullable();
            $table->time('preferred_time_end')->nullable();
            $table->enum('appointment_type', ['consultation', 'follow_up', 'emergency', 'checkup'])->default('consultation');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['waiting', 'notified', 'scheduled', 'expired', 'cancelled'])->default('waiting');
            $table->text('notes')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['business_id', 'status']);
            $table->index(['doctor_id', 'status']);
            $table->index('preferred_date_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waiting_list');
    }
};
