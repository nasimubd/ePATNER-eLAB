<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'patient_id' => 'P-' . $this->faker->unique()->numberBetween(1000, 9999),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->date,
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->optional()->email,
            'address' => $this->faker->address,
            'emergency_contact' => $this->faker->phoneNumber,
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'medical_history' => $this->faker->optional()->paragraph,
            'allergies' => $this->faker->optional()->sentence,
            'current_medications' => $this->faker->optional()->sentence,
        ];
    }
}
