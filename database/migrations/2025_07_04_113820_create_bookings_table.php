<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade'); // Multi-tenant support
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Staff/Admin who created booking
            $table->foreignId('patient_id')->constrained()->onDelete('cascade'); // Patient being booked

            // Polymorphic relationship for Ward or OT services
            $table->morphs('bookable'); // bookable_type, bookable_id

            // For OT bookings only
            $table->foreignId('ot_room_id')->nullable()->constrained()->onDelete('cascade');

            $table->date('booking_date');
            $table->time('booking_time');
            $table->time('end_time')->nullable(); // Calculated end time

            // Additional time fields for OT services
            $table->integer('preparation_time_minutes')->nullable(); // Pre-op setup time
            $table->integer('cleanup_time_minutes')->nullable(); // Post-op cleanup time

            // Fee breakdown
            $table->decimal('service_fee', 10, 2)->default(0);

            $table->enum('booking_type', ['ward', 'ot']);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');

            $table->text('notes')->nullable();

            // Status timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Audit fields
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['business_id', 'booking_date', 'booking_time']);
            $table->index(['business_id', 'bookable_type', 'bookable_id']);
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'booking_type']);
            $table->index(['business_id', 'patient_id']);
            $table->index('user_id');
            $table->index('patient_id');
            $table->index('ot_room_id');

            // Unique constraint to prevent double booking for OT services within same business
            $table->index(['business_id', 'ot_room_id', 'booking_date', 'booking_time'], 'business_ot_room_datetime_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
