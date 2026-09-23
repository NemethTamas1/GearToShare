<?php

namespace Database\Seeders;

use App\Models\Gear;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('email', 'owner@gmail.com')->first();

        if (!$owner) {
            $this->command->warn('GearSeeder: nincs elérhető user, seedelés kihagyva.');
            return;
        }

        Gear::factory()->for($owner)->handTool()->create();
        Gear::factory()->for($owner)->cordless()->create();
    }
}
