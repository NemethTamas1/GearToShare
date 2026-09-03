<?php

namespace Database\Seeders;

use App\Models\Gear;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gears = Gear::all();
        $renters = User::all();

        if ($gears->isEmpty() || $renters->isEmpty()) {
            $this->command->warn('RentalSeeder: nincs elérhető gear vagy user, seedelés kihagyva.');
            return;
        }

        Rental::factory()
            ->for($gears->random())
            ->for($renters->random(), 'renter')
            ->create(); // pending

        Rental::factory()
            ->accepted()
            ->for($gears->random())
            ->for($renters->random(), 'renter')
            ->create();

        Rental::factory()
            ->active()
            ->for($gears->random())
            ->for($renters->random(), 'renter')
            ->create();

        Rental::factory()
            ->completed()
            ->for($gears->random())
            ->for($renters->random(), 'renter')
            ->create();
    }
}
