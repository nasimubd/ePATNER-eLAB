<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_report_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_section_id')->nullable()->constrained('template_sections')->onDelete('set null');
            $table->string('section_name');
            $table->text('section_description')->nullable();
            $table->integer('section_order')->default(0);
            $table->timestamps();

            $table->index(['lab_report_id', 'section_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_report_sections');
    }
};
