<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:renew-subscriptions')->dailyAt('08:00');
