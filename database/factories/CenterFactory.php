<?php

namespace Database\Factories;

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
            'name' => $this->faker->unique()->company . " Masjid",
            'logo' => '',
            'city' => $this->faker->city,
            'wilaya' => $this->faker->state,
            'primaryColor' => $this->faker->hexColor,
            'secondaryColor' => $this->faker->hexColor,
            'accentColor' => $this->faker->hexColor,
            'type' => $this->faker->randomElement(['masjid', 'mousala']),
        ];
    }
}
