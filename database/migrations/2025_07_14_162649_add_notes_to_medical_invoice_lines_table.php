<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_invoice_lines', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('line_total');
        });
    }

    public function down(): void
    {
        Schema::table('medical_invoice_lines', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
