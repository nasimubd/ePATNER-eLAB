<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_ofs', function (Blueprint $table) {
            $table->enum('commission_type', ['fixed', 'percentage'])->default('fixed')->change();
        });
    }

    public function down(): void
    {
        Schema::table('care_ofs', function (Blueprint $table) {
            $table->enum('commission_type', ['fixed'])->default('fixed')->change();
        });
    }
};
