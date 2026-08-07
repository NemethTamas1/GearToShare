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
        $correctPayload = [
            "name" => "Correct User",
            "email" => "correctEmail@gmail.com",
            "phone" => "+36701234567",
            "password" => "correctPassword123",
            
        ];

        $this->post("/api/users", $correctPayload)->assertStatus(201);
    }
}
