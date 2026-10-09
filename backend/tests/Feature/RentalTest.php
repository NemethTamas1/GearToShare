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

    public function test_renter_sees_address_and_owner_phone_after_acceptance(): void
    {
        $owner = User::factory()->create(['phone' => '+36301111111']);
        $renter = User::factory()->create(['phone' => '+36302222222']);
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create();

        $this->actingAs($renter)
            ->getAsFrontend("/api/rentals/{$rental->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.contact.address', $gear->address)
            ->assertJsonPath('data.contact.phone', '+36301111111');
    }

    public function test_owner_sees_renter_phone_but_no_address_after_acceptance(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create(['phone' => '+36302222222']);
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create();

        $this->actingAs($owner)
            ->getAsFrontend("/api/rentals/{$rental->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.contact.phone', '+36302222222')
            ->assertJsonMissingPath('data.contact.address');
    }

    public function test_contact_is_hidden_while_rental_is_pending(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->create();

        $this->actingAs($renter)
            ->getAsFrontend("/api/rentals/{$rental->id}")
            ->assertStatus(200)
            ->assertJsonMissingPath('data.contact');
    }

    public function test_unrelated_user_cannot_view_rental(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $stranger = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create();

        $this->actingAs($stranger)
            ->getAsFrontend("/api/rentals/{$rental->id}")
            ->assertStatus(403);
    }

    public function test_guest_cannot_view_rental(): void
    {
        $rental = Rental::factory()->create();

        $this->getAsFrontend("/api/rentals/{$rental->id}")->assertStatus(401);
    }

    public function test_accepting_a_rental_generates_handover_token(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->create();

        $this->actingAs($owner)
            ->patchAsFrontend("/api/rentals/{$rental->id}", ['status' => 'accepted'])
            ->assertStatus(200);

        $this->assertNotNull($rental->fresh()->handover_token);
    }

    public function test_only_owner_sees_handover_token(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create([
            'handover_token' => 'test-token',
        ]);

        $this->actingAs($owner)
            ->getAsFrontend("/api/rentals/{$rental->id}")
            ->assertJsonPath('data.handover_token', 'test-token');

        $this->actingAs($renter)
            ->getAsFrontend("/api/rentals/{$rental->id}")
            ->assertJsonMissingPath('data.handover_token');
    }

    public function test_renter_can_confirm_handover_with_valid_token(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create([
            'handover_token' => 'test-token',
        ]);

        $this->actingAs($renter)
            ->postAsFrontend("/api/rentals/{$rental->id}/confirm-handover", ['token' => 'test-token'])
            ->assertStatus(200);

        $fresh = $rental->fresh();
        $this->assertSame('active', $fresh->status);
        $this->assertNull($fresh->handover_token);
        $this->assertNotNull($fresh->handover_confirmed_at);
    }

    public function test_confirm_handover_rejects_wrong_token(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create([
            'handover_token' => 'test-token',
        ]);

        $this->actingAs($renter)
            ->postAsFrontend("/api/rentals/{$rental->id}/confirm-handover", ['token' => 'rossz'])
            ->assertStatus(422);

        $this->assertSame('accepted', $rental->fresh()->status);
    }

    public function test_owner_cannot_confirm_handover(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create([
            'handover_token' => 'test-token',
        ]);

        $this->actingAs($owner)
            ->postAsFrontend("/api/rentals/{$rental->id}/confirm-handover", ['token' => 'test-token'])
            ->assertStatus(403);
    }

    public function test_handover_token_cannot_be_used_twice(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create([
            'handover_token' => 'test-token',
        ]);

        $this->actingAs($renter)
            ->postAsFrontend("/api/rentals/{$rental->id}/confirm-handover", ['token' => 'test-token'])
            ->assertStatus(200);

        $this->actingAs($renter)
            ->postAsFrontend("/api/rentals/{$rental->id}/confirm-handover", ['token' => 'test-token'])
            ->assertStatus(422);
    }

    public function test_owner_sees_handover_token(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create([
            'handover_token' => 'test-token',
        ]);

        $this->actingAs($owner)
            ->getAsFrontend("/api/rentals/{$rental->id}")
            ->assertJsonPath('data.handover_token', 'test-token');
    }

    public function test_renter_does_not_see_handover_token(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->accepted()->create([
            'handover_token' => 'test-token',
        ]);

        $this->actingAs($renter)
            ->getAsFrontend("/api/rentals/{$rental->id}")
            ->assertJsonMissingPath('data.handover_token');
    }
}
