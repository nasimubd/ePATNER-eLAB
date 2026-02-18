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
        Schema::table('lab_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('care_of_id')->nullable()->after('advised_by');

            // Add foreign key constraint
            $table->foreign('care_of_id')
                ->references('id')
                ->on('care_ofs')
                ->onDelete('set null');

            // Add index for better query performance
            $table->index('care_of_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_reports', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['care_of_id']);

            // Drop index
            $table->dropIndex(['care_of_id']);

            // Drop column
            $table->dropColumn('care_of_id');
        });
    }
};
