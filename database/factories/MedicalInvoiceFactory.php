<?php

namespace Database\Factories;

use App\Models\MedicalInvoice;
use App\Models\Business;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalInvoiceFactory extends Factory
{
    protected $model = MedicalInvoice::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 5000);
        $discount = $this->faker->randomFloat(2, 0, $subtotal * 0.1);
        $grandTotal = $subtotal - $discount;
        $paidAmount = $this->faker->randomFloat(2, 0, $grandTotal);

        return [
            'business_id' => Business::factory(),
            'invoice_number' => 'INV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'patient_id' => Patient::factory(),
            'invoice_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'payment_method' => $this->faker->randomElement(['cash', 'credit']),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'round_off' => 0,
            'grand_total' => $grandTotal,
            'paid_amount' => $paidAmount,
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'notes' => $this->faker->optional()->sentence,
            'payment_notes' => $this->faker->optional()->sentence,
            'created_by' => User::factory(),
        ];
    }
}
