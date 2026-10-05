<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_id' => Rental::factory()->completed(),
            'rater_id' => User::factory(),
            'rated_id' => User::factory(),
            'score' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional()->sentence(),
        ];
    }
}
