<?php

namespace Database\Factories;

use App\Models\Gear;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+2 weeks');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 7) . ' days');

        return [
            'gear_id' => Gear::factory(),
            'renter_id' => User::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'status' => 'pending',
            'handover_token' => null,
            'handover_confirmed_at' => null,
            'barion_payment_id' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn() => [
            'status' => 'accepted',
            'handover_token' => $this->faker->uuid(),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn() => [
            'status' => 'active',
            'handover_confirmed_at' => now(),
            'barion_payment_id' => 'mock_barion_' . $this->faker->uuid(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn() => [
            'status' => 'completed',
            'handover_confirmed_at' => now()->subDays(3),
            'barion_payment_id' => 'mock_barion_' . $this->faker->uuid(),
        ]);
    }
}
