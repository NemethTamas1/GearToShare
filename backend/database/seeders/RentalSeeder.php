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
        $renter = User::where('email', 'renter@gmail.com')->first();

        if ($gears->isEmpty() || !$renter) {
            $this->command->warn('RentalSeeder: nincs elérhető gear vagy user, seedelés kihagyva.');
            return;
        }

        Rental::factory()
            ->for($gears->random())
            ->for($renter, 'renter')
            ->create(); // pending

        Rental::factory()
            ->accepted()
            ->for($gears->random())
            ->for($renter, 'renter')
            ->create();
    }
}
