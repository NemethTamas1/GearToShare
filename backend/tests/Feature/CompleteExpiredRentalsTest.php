<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompleteExpiredRentalsTest extends TestCase
{
    use RefreshDatabase;

    private function rentalWith(string $state, string $endDate): Rental
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();

        return Rental::factory()->for($gear)->for($renter, 'renter')->{$state}()->create([
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => $endDate,
        ]);
    }

    public function test_active_rental_past_end_date_becomes_completed(): void
    {
        $rental = $this->rentalWith('active', now()->subDay()->toDateString());

        $this->artisan('rentals:complete-expired')->assertSuccessful();

        $this->assertSame('completed', $rental->fresh()->status);
    }

    public function test_active_rental_ending_today_stays_active(): void
    {
        $rental = $this->rentalWith('active', now()->toDateString());

        $this->artisan('rentals:complete-expired')->assertSuccessful();

        $this->assertSame('active', $rental->fresh()->status);
    }

    public function test_accepted_rental_is_not_completed_even_if_expired(): void
    {
        $rental = $this->rentalWith('accepted', now()->subDay()->toDateString());

        $this->artisan('rentals:complete-expired')->assertSuccessful();

        $this->assertSame('accepted', $rental->fresh()->status);
    }
}
