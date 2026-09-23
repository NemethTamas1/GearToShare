<?php

namespace Database\Factories;

use App\Models\Gear;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gear>
 */
class GearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'category' => 'hand_tool',
            'attributes' => null,
            'price_per_day' => $this->faker->randomFloat(2, 5, 100),
            'city' => $this->faker->city(),
            'address' => $this->faker->streetAddress(),
            'status' => 'available',
        ];
    }

    public function handTool(): static
    {
        return $this->state(fn() => [
            'category' => 'hand_tool',
            'attributes' => null
        ]);
    }

    public function cordless(): static
    {
        return $this->state(fn() => [
            'category' => 'cordless',
            'attributes' => [
                'battery_capacity_mah' => $this->faker->randomElement([1500, 2000, 4000, 5000]),
                'modes' => $this->faker->randomElements(['drill', 'hammer_drill', 'chiseling'], $this->faker->numberBetween(0, 3)),
            ],
        ]);
    }

    public function corded():static
    {
        return $this->state(fn() => [
            'category' => 'corded',
            'attributes' => [
                'power_w' => $this->faker->randomElement([650, 850, 1100, 1700]),
                'modes' => $this->faker->randomElements(['drill', 'hammer_drill', 'chiseling'], $this->faker->numberBetween(0, 3)),
            ],
        ]);
    }

    public function machine(): static
    {
        return $this->state(fn() => [
            'category' => 'machine',
            'attributes' => [
                'load_capacity_kg' => $this->faker->numberBetween(200, 2000),
                'fuel_type' => $this->faker->randomElement(['benzin', 'dizel', 'elektromos']),
                'fuel_tank_l' => $this->faker->randomFloat(1, 5, 60),
                'horsepower' => $this->faker->numberBetween(10, 150),
            ],
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn() => ['status' => 'draft']);
    }
}
