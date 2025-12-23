<?php

namespace App\Formatter;

use App\DTO\UserDTO;

class UserFormatter
{
    /**
     * @param UserDTO $userDTO
     * @return array{string, string}
     */
    public function format(UserDTO $userDTO): array
    {
        return [
            'nickname' => $userDTO->getNickname(),
            'avatar' => asset('storage/' . $userDTO->getAvatarPath()),
        ];
    }
}
