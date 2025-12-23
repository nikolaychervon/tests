<?php

use App\Jobs\CleanupOldUsers;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new CleanupOldUsers())->everyMinute()->withoutOverlapping();;
