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
        User::factory()->create([
            "name" => "Kiss Béla",
            "email" => "owner@gmail.com"
        ]);

        User::factory()->create([
            "name" => "Varga Róbert",
            "email" => "renter@gmail.com"
        ]);

        $this->call([
            GearSeeder::class,
            RentalSeeder::class,
        ]);
    }
}
