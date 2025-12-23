<?php

namespace App\Services;

use App\DTO\UserDTO;
use App\Formatter\UserFormatter;
use App\Repositories\UserRedisRepository;
use Illuminate\Validation\ValidationException;

class UserRegisterService
{
    public function __construct(
        private UserRedisRepository $userRedisRepository,
        private UserFormatter $userFormatter,
    ) {
    }

    /**
     * @param UserDTO $userDTO
     * @return array{nickname: string, avatar: string}
     * @throws ValidationException
     */
    public function register(UserDTO $userDTO): array
    {
        if ($this->userRedisRepository->exists($userDTO->getNickname())) {
            throw ValidationException::withMessages([
                UserDTO::NICKNAME => ['This nickname is already taken.']
            ]);
        }

        $avatarPath = $userDTO->getAvatar()->store('avatars', 'public');
        $userDTO->setAvatarPath($avatarPath);

        $this->userRedisRepository->save($userDTO);
        return $this->userFormatter->format($userDTO);
    }
}
