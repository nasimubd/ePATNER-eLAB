<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'transaction_type' => $this->faker->randomElement(['Payment', 'Receipt', 'Journal', 'Contra']),
            'transaction_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'narration' => $this->faker->sentence,
        ];
    }
}
