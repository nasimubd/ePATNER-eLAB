<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ot_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade'); // Multi-tenant support
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_fee', 10, 2);
            $table->decimal('room_fee', 10, 2)->default(0);
            $table->decimal('equipment_fee', 10, 2)->default(0);
            $table->integer('estimated_duration_minutes');
            $table->integer('preparation_time_minutes')->default(30);
            $table->integer('cleanup_time_minutes')->default(30);
            $table->json('required_equipment')->nullable();
            $table->json('required_staff')->nullable();
            $table->enum('complexity_level', ['minor', 'major', 'critical'])->default('minor');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index('complexity_level');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ot_services');
    }
};
