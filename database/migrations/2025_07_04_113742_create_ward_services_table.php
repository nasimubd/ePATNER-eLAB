<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ward_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade'); // Multi-tenant support
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('daily_fee', 10, 2);
            $table->integer('duration_minutes')->default(60);
            $table->integer('max_patients_per_slot')->default(1);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('available_days')->nullable(); // ['monday', 'tuesday', etc.]
            $table->time('start_time')->default('09:00:00');
            $table->time('end_time')->default('17:00:00');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ward_services');
    }
};
