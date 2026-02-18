<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('test_name');
            $table->string('test_code')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_minutes')->nullable(); // Test duration in minutes
            $table->text('instructions')->nullable();
            $table->text('preparation_instructions')->nullable();
            $table->string('sample_type')->nullable(); // Blood, Urine, etc.
            $table->string('department')->nullable(); // Pathology, Radiology, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_tests');
    }
};
