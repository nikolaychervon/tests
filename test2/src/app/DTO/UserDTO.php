<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;

class UserDTO
{
    public const string NICKNAME = 'nickname';
    public const string AVATAR = 'avatar_path';
    public const string CREATED_AT = 'created_at';

    public function __construct(
        private string $nickname,
        private int $createdAt,
        private ?UploadedFile $avatar = null,
        private string $avatarPath = '',
    ) {
    }

    /**
     * @return array{nickname: string, avatar_path: string, created_at: int}
     */
    public function toArray(): array
    {
        return [
            self::NICKNAME => $this->nickname,
            self::AVATAR => $this->avatarPath,
            self::CREATED_AT => $this->createdAt,
        ];
    }

    /**
     * @param array{nickname: string, avatar_path: string, created_at: int} $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            nickname: $data[self::NICKNAME],
            createdAt: $data[self::CREATED_AT],
            avatarPath: $data[self::AVATAR]
        );
    }

    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }

    /**
     * @return UploadedFile|null
     */
    public function getAvatar(): ?UploadedFile
    {
        return $this->avatar;
    }

    /**
     * @param string $avatarPath
     * @return void
     */
    public function setAvatarPath(string $avatarPath): void
    {
        $this->avatarPath = $avatarPath;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getAvatarPath(): string
    {
        return $this->avatarPath;
    }
}
