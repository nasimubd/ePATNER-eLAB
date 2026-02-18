<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_invoices', function (Blueprint $table) {
            $table->foreignId('care_of_id')->nullable()->after('patient_id')->constrained('care_ofs')->onDelete('set null');
            $table->index('care_of_id');
        });
    }

    public function down(): void
    {
        Schema::table('medical_invoices', function (Blueprint $table) {
            $table->dropForeign(['care_of_id']);
            $table->dropIndex(['care_of_id']);
            $table->dropColumn('care_of_id');
        });
    }
};
