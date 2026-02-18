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
        Schema::table('ledgers', function (Blueprint $table) {
            // Add ledger_type column for accounting classification
            $table->string('ledger_type')->after('location');

            // Add opening_balance column
            $table->decimal('opening_balance', 15, 2)->default(0)->after('ledger_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            $table->dropColumn(['ledger_type', 'opening_balance']);
        });
    }
};
