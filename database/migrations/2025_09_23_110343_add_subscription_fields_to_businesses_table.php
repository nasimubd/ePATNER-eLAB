<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('is_active');
            $table->decimal('custom_monthly_fee', 10, 2)->nullable()->after('due_date');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['due_date']);
            $table->dropColumn(['due_date', 'custom_monthly_fee']);
        });
    }
};
