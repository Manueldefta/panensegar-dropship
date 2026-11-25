<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('orders:expire-unpaid')->everyFifteenMinutes();
Schedule::command('harvest:summary')->dailyAt('20:05')->timezone('Asia/Jakarta');
