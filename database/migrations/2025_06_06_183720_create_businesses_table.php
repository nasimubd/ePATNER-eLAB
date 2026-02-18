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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_name');
            $table->text('address');
            $table->string('contact_number');
            $table->string('email')->nullable(); // Optional email field
            $table->longText('logo')->nullable(); // For storing base64 encoded image data
            $table->string('logo_mime_type')->nullable(); // For storing image MIME type
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add indexes for better performance
            $table->index('hospital_name');
            $table->index('is_active');
            $table->index('email');
            $table->index(['is_active', 'hospital_name']); // Composite index for common queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
