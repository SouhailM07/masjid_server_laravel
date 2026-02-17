<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
return [
            // If center_id is not passed when creating, default to null
            'center_id' => $this->faker->numberBetween(1, 10), 
            'value' => $this->faker->unique()->phoneNumber,
            'type' => $this->faker->randomElement(['phone', 'email']),
        ];
    }
}
