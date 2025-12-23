<?php

namespace App\Services;

use App\Formatter\UserFormatter;
use App\Repositories\UserRedisRepository;

class UsersGettingService
{
    public function __construct(
        private UserRedisRepository $userRedisRepository,
        private UserFormatter $userFormatter
    ) {
    }

    /**
     * @return array{int, array{string, string}}
     */
    public function getList(): array
    {
        $users = [];
        foreach ($this->userRedisRepository->all() as $userDTO) {
            $users[] = $this->userFormatter->format($userDTO);
        }

        return $users;
    }
}
