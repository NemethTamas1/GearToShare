<?php

namespace Database\Seeders;

use App\Models\Gear;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $owners = User::factory()->count(3)->create();
        User::factory()->count(5)->create();

        $owners->each(function (User $owner) {
            Gear::factory()->for($owner)->handTool()->create();
            Gear::factory()->for($owner)->powerTool()->create();
            Gear::factory()->for($owner)->machine()->create();
        });

        $this->call(RentalSeeder::class);
    }
}
