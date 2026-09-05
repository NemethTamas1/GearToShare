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
        $owners = User::all();

        if ($owners->isEmpty()) {
            $this->command->warn('GearSeeder: nincs elérhető user, seedelés kihagyva.');
            return;
        }

        $owners->each(function (User $owner) {
            Gear::factory()->for($owner)->handTool()->create();
            Gear::factory()->for($owner)->powerTool()->create();
            Gear::factory()->for($owner)->machine()->create();
        });
    }
}
