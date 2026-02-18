<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_report_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_section_id')->constrained('lab_report_sections')->onDelete('cascade');
            $table->foreignId('template_field_id')->nullable()->constrained('template_fields')->onDelete('set null');
            $table->string('field_name');
            $table->string('field_label');
            $table->text('field_value')->nullable();
            $table->string('unit')->nullable();
            $table->string('normal_range')->nullable();
            $table->boolean('is_abnormal')->default(false);
            $table->integer('field_order')->default(0);
            $table->timestamps();

            $table->index(['report_section_id', 'field_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_report_fields');
    }
};
