<?php

namespace Tests\Feature;

use App\Jobs\CleanupOldUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OldUsersDeletedJobApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function jobForClearingOldUsersIsDispatched(): void
    {
        Queue::fake();
        $this->artisan('schedule:run');
        Queue::assertPushed(CleanupOldUsers::class);
    }
}
