<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lab_test_medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lab_test_id');
            $table->unsignedBigInteger('medicine_id');
            $table->integer('quantity_required');
            $table->text('usage_instructions')->nullable();
            $table->timestamps();

            $table->foreign('lab_test_id')->references('id')->on('lab_tests')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->unique(['lab_test_id', 'medicine_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_test_medicines');
    }
};
