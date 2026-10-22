<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('rentals:complete-expired')->dailyAt('00:10');
