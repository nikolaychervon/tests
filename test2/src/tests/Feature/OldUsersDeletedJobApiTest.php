<?php

namespace Tests\Feature;

use App\Jobs\CleanupOldUsers;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OldUsersDeletedJobApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function job_for_clearing_old_users_is_dispatched()
    {
        Queue::fake();
        $this->artisan('schedule:run');
        Queue::assertPushed(CleanupOldUsers::class);
    }
}
