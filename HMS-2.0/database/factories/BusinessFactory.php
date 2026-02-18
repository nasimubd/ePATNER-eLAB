<?php

namespace Database\Factories;

use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'hospital_name' => $this->faker->company . ' Medical Center',
            'address' => $this->faker->address,
            'contact_number' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'is_active' => true,
            'enable_a5_printing' => false,
        ];
    }
}
