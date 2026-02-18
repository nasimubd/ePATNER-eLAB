<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ot_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade'); // Multi-tenant support
            $table->string('name'); // OT-1, OT-2, Main Theatre, etc.
            $table->string('room_number');
            $table->text('description')->nullable();
            $table->json('equipment_available')->nullable(); // ['ventilator', 'monitors', etc.]
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->integer('capacity')->default(1); // Usually 1 for OT
            $table->timestamps();
            $table->softDeletes();

            // Updated indexes with business_id
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'room_number']);
            $table->index('room_number');

            // Unique constraint: room_number should be unique within each business
            $table->unique(['business_id', 'room_number'], 'unique_business_room_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ot_rooms');
    }
};
