<?php

namespace App\Jobs;

use App\DTO\UserDTO;
use App\Repositories\UserRedisRepository;
use App\Services\UserDeletingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanupOldUsers implements ShouldQueue
{
    use Queueable;

    private const int OLDER_THAN_MINUTES = 1;

    private UserRedisRepository $userRedisRepository;
    private UserDeletingService $userDeletingService;
    private int $olderThanMinutes;

    public function __construct(int $olderThanMinutes = self::OLDER_THAN_MINUTES)
    {
        $this->userRedisRepository = app(UserRedisRepository::class);
        $this->userDeletingService = app(UserDeletingService::class);
        $this->olderThanMinutes = $olderThanMinutes;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $now = now()->timestamp;

        /** @var UserDTO $user */
        foreach ($this->userRedisRepository->all() as $user) {
            if (($now - $user->getCreatedAt()) > ($this->olderThanMinutes * 60)) {
                $this->userDeletingService->delete($user);
            }
        }
    }
}
