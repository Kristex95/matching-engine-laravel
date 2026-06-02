<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('outbox:drain')->everyTenSeconds()->withoutOverlapping();