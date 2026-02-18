<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->enum('old_status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->nullable();
            $table->enum('new_status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show']);
            $table->text('reason')->nullable();
            $table->json('changed_fields')->nullable();
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index('booking_id');
            $table->index('changed_by');
            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_histories');
    }
};
