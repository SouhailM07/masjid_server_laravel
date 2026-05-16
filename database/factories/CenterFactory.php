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
        $type = $this->faker->randomElement(['masjid', 'mousala']);

return [
            'name' => $this->faker->unique()->company ,
            'type' => $type,
            'logo' => $type === 'masjid' ? 'defaults/mosque-logo.png' : 'defaults/mousala-logo.png',
            'city' => $this->faker->city,
            'wilaya' => $this->faker->state,
            'longitude'=>fake()->randomDigit(),
            'latitude'=>fake()->randomDigit(),
        ];
    }
}
