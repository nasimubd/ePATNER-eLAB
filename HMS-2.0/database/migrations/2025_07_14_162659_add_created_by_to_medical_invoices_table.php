<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_invoices', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('notes')->constrained('users')->onDelete('set null');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('medical_invoices', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
