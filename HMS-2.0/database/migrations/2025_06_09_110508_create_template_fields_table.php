<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('template_sections')->onDelete('cascade');
            $table->string('field_name');
            $table->string('field_label');
            $table->enum('field_type', ['text', 'number', 'select', 'textarea', 'date', 'time']);
            $table->json('field_options')->nullable(); // For select options
            $table->string('default_value')->nullable();
            $table->string('unit')->nullable(); // e.g., mg/dl, cells/hpf
            $table->string('normal_range')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('field_order')->default(0);
            $table->timestamps();

            $table->index(['section_id', 'field_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_fields');
    }
};
