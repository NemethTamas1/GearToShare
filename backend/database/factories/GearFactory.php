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
            'attribues' => null
        ]);
    }

    public function powerTool(): static
    {
        return $this->state(fn() => [
            'category' => 'power_tool',
            'attributes' => [
                'battery_capacity_mAh' => $this->faker->randomElement([1500, 2000, 4000, 5000]),
                'modes' => $this->faker->randomElements(['furas, utvefuras', 'csavarozas'], $this->faker->numberBetween(0, 3))
            ]
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
