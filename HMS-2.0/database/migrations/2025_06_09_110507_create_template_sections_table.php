<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('report_templates')->onDelete('cascade');
            $table->string('section_name');
            $table->text('section_description')->nullable();
            $table->integer('section_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->index(['template_id', 'section_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_sections');
    }
};
