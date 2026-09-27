<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $payload = [
            "name" => "Correct User",
            "email" => "correctEmail@gmail.com",
            "phone" => "+36701234567",
            "password" => "correctPassword123",
            "password_confirmation" => "correctPassword123",
        ];

        $this->postAsFrontend("/api/register", $payload)->assertStatus(200);
    }

    public function test_registration_fails_without_required_fields(): void
    {
        $this->post("/api/register", [])->assertStatus(422);
    }

    public function test_registration_fails_when_passwords_dont_match(): void
    {
        $payload = [
            "name" => "Correct User",
            "email" => "correctEmail@gmail.com",
            "phone" => "+36701234567",
            "password" => "correctPassword123",
            "password_confirmation" => "somethingElse",
        ];

        $this->post("/api/register", $payload)->assertStatus(422);
    }
}
