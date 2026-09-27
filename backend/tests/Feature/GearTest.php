<?php

namespace Tests\Feature;

use App\Models\Gear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GearTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_hand_tool_gear(): void
    {
        $user = User::factory()->create();

        $payload = [
            "title" => "Teszt kalapács",
            "description" => "Sima kalapács",
            "category" => "hand_tool",
            "price_per_day" => "1000.00",
            "city" => "Budapest",
            "address" => "Teszt utca 1.",
        ];

        $this->actingAs($user)
            ->postAsFrontend("/api/gears", $payload)
            ->assertStatus(201);

        $this->assertDatabaseHas('gears', [
            'title' => 'Teszt kalapács',
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_cannot_create_gear(): void
    {
        $payload = [
            "title" => "Teszt kalapács",
            "category" => "hand_tool",
            "price_per_day" => "1000.00",
            "city" => "Budapest",
            "address" => "Teszt utca 1.",
        ];

        $this->postAsFrontend("/api/gears", $payload)
            ->assertStatus(401);
    }

    public function test_gear_creation_fails_without_required_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postAsFrontend("/api/gears", [])
            ->assertStatus(422);
    }

    public function test_cordless_gear_requires_battery_capacity(): void
    {
        $user = User::factory()->create();

        $payload = [
            "title" => "Akkus fúró",
            "category" => "cordless",
            "price_per_day" => "2000.00",
            "city" => "Budapest",
            "address" => "Teszt utca 1.",
            // battery_capacity_mah hiányzik
        ];

        $this->actingAs($user)
            ->postAsFrontend("/api/gears", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['attributes.battery_capacity_mah']);
    }

    public function test_hand_tool_gear_rejects_foreign_category_attributes(): void
    {
        $user = User::factory()->create();

        $payload = [
            "title" => "Sima kalapács",
            "category" => "hand_tool",
            "price_per_day" => "1000.00",
            "city" => "Budapest",
            "address" => "Teszt utca 1.",
            "attributes" => [
                "power_w" => 500, // nem odavaló mező hand_tool-hoz
            ],
        ];

        $this->actingAs($user)
            ->postAsFrontend("/api/gears", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['attributes.power_w']);
    }

    public function test_owner_can_update_own_gear()
    {
        $owner = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();

        $payload = [
            "title" => "Frissített cím",
            "description" => $gear->description,
            "category" => $gear->category,
            "price_per_day" => "1500.00",
            "city" => $gear->city,
            "address" => $gear->address,
        ];

        $this->actingAs($owner)
            ->putAsFrontend("/api/gears/{$gear->id}", $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('gears', [
            'id' => $gear->id,
            'title' => 'Frissített cím',
        ]);
    }

    public function test_non_owner_cannot_update_gear(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();

        $payload = [
            "title" => "Illetéktelen módosítás",
            "description" => $gear->description,
            "category" => $gear->category,
            "price_per_day" => "1500.00",
            "city" => $gear->city,
            "address" => $gear->address,
        ];

        $this->actingAs($otherUser)
            ->putAsFrontend("/api/gears/{$gear->id}", $payload)
            ->assertStatus(403);

        $this->assertDatabaseMissing('gears', [
            'id' => $gear->id,
            'title' => 'Illetéktelen módosítás',
        ]);
    }

    public function test_owner_can_delete_own_gear(): void
    {
        $owner = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();

        $this->actingAs($owner)
            ->deleteAsFrontend("/api/gears/{$gear->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('gears', ['id' => $gear->id]);
    }

    public function test_non_owner_cannot_delete_gear(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $gear = Gear::factory()->for($owner)->handTool()->create();

        $this->actingAs($otherUser)
            ->deleteAsFrontend("/api/gears/{$gear->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('gears', ['id' => $gear->id]);
    }
}
