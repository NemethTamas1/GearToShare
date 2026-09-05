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
        User::factory()->count(3)->create(); // bérbeadók
        User::factory()->count(5)->create(); // bérlők

        $this->call([
            GearSeeder::class,
            RentalSeeder::class,
        ]);
    }
}
