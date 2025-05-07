<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'type' => $this->faker->randomElement(['salary', 'expense', 'bonus', 'advance']),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['paid', 'pending', 'rejected']),
            'description' => $this->faker->sentence(),
        ];
    }
    
}
