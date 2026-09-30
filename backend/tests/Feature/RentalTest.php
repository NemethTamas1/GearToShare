<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RentalTest extends TestCase
{

    use RefreshDatabase;

    public function test_authenticated_user_can_create_rental_request(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();

        $payload = [
            'gear_id' => $gear->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'message' => 'Szia, hétvégére kellene.',
        ];

        $this->actingAs($renter)
            ->postAsFrontend('/api/rentals', $payload)
            ->assertStatus(201);

        $this->assertDatabaseHas('rentals', [
            'gear_id' => $gear->id,
            'renter_id' => $renter->id,
            'status' => 'pending',
        ]);
    }

    public function test_guest_cannot_create_rental_request(): void
    {
        $owner = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();

        $payload = [
            'gear_id' => $gear->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ];

        $this->postAsFrontend('/api/rentals', $payload)->assertStatus(401);
    }

    public function test_owner_cannot_rent_own_gear(): void
    {
        $owner = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();

        $payload = [
            'gear_id' => $gear->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ];

        $this->actingAs($owner)
            ->postAsFrontend('/api/rentals', $payload)
            ->assertStatus(422);
    }

    public function test_rental_creation_fails_without_required_fields(): void
    {
        $renter = User::factory()->create();

        $this->actingAs($renter)
            ->postAsFrontend('/api/rentals', [])
            ->assertStatus(422);
    }

    public function test_rental_total_price_is_calculated_from_gear_price_and_days(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create(['price_per_day' => '1000.00']);

        $payload = [
            'gear_id' => $gear->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(), // 3 nap
        ];

        $this->actingAs($renter)
            ->postAsFrontend('/api/rentals', $payload)
            ->assertStatus(201);

        $this->assertDatabaseHas('rentals', [
            'gear_id' => $gear->id,
            'total_price' => '3000.00',
        ]);
    }

    public function test_owner_can_accept_pending_rental(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->create();

        $this->actingAs($owner)
            ->patchAsFrontend("/api/rentals/{$rental->id}", ['status' => 'accepted'])
            ->assertStatus(200);

        $this->assertDatabaseHas('rentals', ['id' => $rental->id, 'status' => 'accepted']);
    }

    public function test_owner_can_reject_pending_rental(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->create();

        $this->actingAs($owner)
            ->patchAsFrontend("/api/rentals/{$rental->id}", ['status' => 'rejected'])
            ->assertStatus(200);

        $this->assertDatabaseHas('rentals', ['id' => $rental->id, 'status' => 'rejected']);
    }

    public function test_renter_cannot_change_own_rental_status(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->create();

        $this->actingAs($renter)
            ->patchAsFrontend("/api/rentals/{$rental->id}", ['status' => 'accepted'])
            ->assertStatus(403);

        $this->assertDatabaseHas('rentals', ['id' => $rental->id, 'status' => 'pending']);
    }

    public function test_unrelated_user_cannot_change_rental_status(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $stranger = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->create();

        $this->actingAs($stranger)
            ->patchAsFrontend("/api/rentals/{$rental->id}", ['status' => 'accepted'])
            ->assertStatus(403);
    }

    public function test_owner_cannot_change_status_of_non_pending_rental(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create();

        $this->actingAs($owner)
            ->patchAsFrontend("/api/rentals/{$rental->id}", ['status' => 'rejected'])
            ->assertStatus(422);
    }

    public function test_rental_status_rejects_invalid_value(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->create();

        $this->actingAs($owner)
            ->patchAsFrontend("/api/rentals/{$rental->id}", ['status' => 'active'])
            ->assertStatus(422);
    }
}
