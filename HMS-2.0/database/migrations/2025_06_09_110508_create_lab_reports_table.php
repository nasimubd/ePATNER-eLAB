<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade'); // Changed from customer_ledger_id
            $table->foreignId('lab_test_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->constrained('report_templates')->onDelete('cascade');
            $table->date('report_date');
            $table->string('advised_by')->default('SELF');
            $table->text('investigation_details')->nullable();
            $table->text('technical_notes')->nullable();
            $table->text('doctor_comments')->nullable();
            $table->enum('status', ['draft', 'completed', 'verified', 'delivered'])->default('draft');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['business_id', 'patient_id']);
            $table->index(['business_id', 'report_date']);
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_reports');
    }
};
