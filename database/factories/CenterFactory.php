<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Center>
 */
class CenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
return [
            'name' => $this->faker->unique()->company ,
            'logo' => '',
            'city' => $this->faker->city,
            'wilaya' => $this->faker->state,
            'longitude'=>fake()->randomDigit(),
            'latitude'=>fake()->randomDigit(),
            'user_id'=>User::inRandomOrder()->value('id'),
            'type' => $this->faker->randomElement(['masjid', 'mousala']),
        ];
    }
}
