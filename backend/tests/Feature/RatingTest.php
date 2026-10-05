<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    private function completedRental(): array
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->completed()->create();

        return [$owner, $renter, $rental];
    }

    public function test_renter_can_rate_owner_after_completed_rental(): void
    {
        [$owner, $renter, $rental] = $this->completedRental();

        $this->actingAs($renter)
            ->postAsFrontend("/api/rentals/{$rental->id}/ratings", ['score' => 5, 'comment' => 'Korrekt volt.'])
            ->assertStatus(201);

        $this->assertDatabaseHas('ratings', [
            'rental_id' => $rental->id,
            'rater_id' => $renter->id,
            'rated_id' => $owner->id,
            'score' => 5,
        ]);
    }

    public function test_owner_can_rate_renter_after_completed_rental(): void
    {
        [$owner, $renter, $rental] = $this->completedRental();

        $this->actingAs($owner)
            ->postAsFrontend("/api/rentals/{$rental->id}/ratings", ['score' => 4])
            ->assertStatus(201);

        $this->assertDatabaseHas('ratings', [
            'rater_id' => $owner->id,
            'rated_id' => $renter->id,
        ]);
    }

    public function test_unrelated_user_cannot_rate(): void
    {
        [,, $rental] = $this->completedRental();
        $stranger = User::factory()->create();

        $this->actingAs($stranger)
            ->postAsFrontend("/api/rentals/{$rental->id}/ratings", ['score' => 5])
            ->assertStatus(403);
    }

    public function test_cannot_rate_non_completed_rental(): void
    {
        $owner = User::factory()->create();
        $renter = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();
        $rental = Rental::factory()->for($gear)->for($renter, 'renter')->active()->create();

        $this->actingAs($renter)
            ->postAsFrontend("/api/rentals/{$rental->id}/ratings", ['score' => 5])
            ->assertStatus(422);
    }

    public function test_cannot_rate_same_rental_twice(): void
    {
        [, $renter, $rental] = $this->completedRental();

        $this->actingAs($renter)
            ->postAsFrontend("/api/rentals/{$rental->id}/ratings", ['score' => 5])
            ->assertStatus(201);

        $this->actingAs($renter)
            ->postAsFrontend("/api/rentals/{$rental->id}/ratings", ['score' => 3])
            ->assertStatus(422);
    }

    public function test_rating_rejects_invalid_score(): void
    {
        [, $renter, $rental] = $this->completedRental();

        foreach ([0, 6, 'abc'] as $bad) {
            $this->actingAs($renter)
                ->postAsFrontend("/api/rentals/{$rental->id}/ratings", ['score' => $bad])
                ->assertStatus(422);
        }
    }

    public function test_guest_cannot_rate(): void
    {
        [,, $rental] = $this->completedRental();

        $this->postAsFrontend("/api/rentals/{$rental->id}/ratings", ['score' => 5])
            ->assertStatus(401);
    }

    public function test_received_ratings_returns_average_and_count_for_current_user_only(): void
    {
        [$owner, $renter, $rental] = $this->completedRental();

        $this->actingAs($renter)
            ->postAsFrontend("/api/rentals/{$rental->id}/ratings", ['score' => 4])
            ->assertStatus(201);

        $this->actingAs($owner)
            ->getAsFrontend('/api/myratings')
            ->assertStatus(200)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('average', 4);

        $this->actingAs($renter)
            ->getAsFrontend('/api/myratings')
            ->assertStatus(200)
            ->assertJsonPath('count', 0)
            ->assertJsonPath('average', null);
    }
}
