<?php

namespace App\Services;

use App\DTO\UserDTO;
use App\Repositories\UserRedisRepository;
use Illuminate\Support\Facades\Storage;

class UserDeletingService
{
    public function __construct(private UserRedisRepository $userRedisRepository)
    {
    }

    /**
     * @param UserDTO $userDTO
     * @return void
     */
    public function delete(UserDTO $userDTO): void
    {
        Storage::disk('public')->delete($userDTO->getAvatarPath());
        $this->userRedisRepository->delete($userDTO->getNickname());
    }
}
