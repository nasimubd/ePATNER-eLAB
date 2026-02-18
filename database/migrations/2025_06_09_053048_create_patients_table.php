<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id')->unique(); // TH-001, QKC-001, etc.
            $table->foreignId('business_id')->constrained()->onDelete('cascade');

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->virtualAs("concat(first_name, ' ', last_name)");
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('blood_group')->nullable();
            $table->string('marital_status')->nullable();

            // Contact Information
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Bangladesh');

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            // Medical Information
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();
            $table->text('current_medications')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_number')->nullable();

            // Additional Information
            $table->string('occupation')->nullable();
            $table->string('national_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('profile_image')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index(['business_id', 'patient_id']);
            $table->index(['business_id', 'phone']);
            $table->index(['business_id', 'is_active']);
            $table->index(['first_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
