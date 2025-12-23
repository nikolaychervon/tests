<?php

namespace App\Repositories;
use App\DTO\UserDTO;
use Illuminate\Support\Facades\Cache;

class UserRedisRepository
{
    private const string KEY = 'users';

    /**
     * @param string $nickname
     * @return bool
     */
    public function exists(string $nickname): bool
    {
        $users = $this->getUsersFromCache();
        return isset($users[$nickname]);
    }

    /**
     * @param UserDTO $user
     * @return void
     */
    public function save(UserDTO $user): void
    {
        $users = $this->getUsersFromCache();
        $users[$user->getNickname()] = $user->toArray();
        Cache::put(self::KEY, $users);
    }

    /**
     * @return array{int, UserDTO}
     */
    public function all(): array
    {
        $users = $this->getUsersFromCache();
        return array_map(fn($data) => UserDTO::fromArray($data), $users);
    }

    /**
     * @param string $nickname
     * @return void
     */
    public function delete(string $nickname): void
    {
        $users = $this->getUsersFromCache();
        if (isset($users[$nickname])) {
            unset($users[$nickname]);
            Cache::put(self::KEY, $users);
        }
    }

    /**
     * @return array
     */
    private function getUsersFromCache(): array
    {
        return Cache::get(self::KEY, []);
    }
}
