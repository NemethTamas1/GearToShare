<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rentals:complete-expired')]
#[Description('Az end_date-jüket túllépett aktív bérléseket lezárja (completed).')]
class CompleteExpiredRentals extends Command
{
    public function handle()
    {
        $count = Rental::where('status', 'active')
            ->whereDate('end_date', '<', today())
            ->update(['status' => 'completed']);

        $this->info("{$count} bérlés lezárva.");

        return self::SUCCESS;
    }
}
