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
        Schema::table('print_requests', function (Blueprint $table) {
            $table->integer('allowed_prints')->default(1)->after('status');
            $table->integer('prints_used')->default(0)->after('allowed_prints');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_requests', function (Blueprint $table) {
            $table->dropColumn(['allowed_prints', 'prints_used']);
        });
    }
};
