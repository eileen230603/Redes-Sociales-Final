<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('eventos:finalizar-vencidos')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
