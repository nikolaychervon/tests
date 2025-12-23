<?php

namespace Tests\Feature;

use App\Repositories\UserRedisRepository;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRegisterApiTest extends TestCase
{
    use RefreshDatabase;

    private const string BASE_NICKNAME = 'test_user';
    private const string NICKNAME_FIELD = 'nickname';
    private const string AVATAR_FIELD = 'avatar';
    private const string AVATAR_PATH_FIELD = 'avatar_path';
    private const string API_METHOD = '/api/register';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    #[Test]
    public function canRegisterUserWithValidData(): void
    {
        $avatar = UploadedFile::fake()->image('avatar.jpg')->size(1024);

        $response = $this->postJson(self::API_METHOD, [
            self::NICKNAME_FIELD => self::BASE_NICKNAME,
            self::AVATAR_FIELD => $avatar,
        ]);

        $response->assertStatus(200);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $disk->assertExists('avatars/' . $avatar->hashName());

        $users = Cache::get(UserRedisRepository::KEY);
        $this->assertNotNull($users[self::BASE_NICKNAME]);
        $this->assertEquals($users[self::BASE_NICKNAME][self::NICKNAME_FIELD], self::BASE_NICKNAME);
        $this->assertEquals($users[self::BASE_NICKNAME][self::AVATAR_PATH_FIELD], 'avatars/' . $avatar->hashName());
    }

    #[Test]
    public function cannotRegisterUserWithVoidData(): void
    {
        $response = $this->postJson(self::API_METHOD);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannotRegisterUserWithDuplicatedNickname(): void
    {
        $avatar = UploadedFile::fake()->image('avatar.jpg')->size(1024);

        $this->postJson(self::API_METHOD, [
            self::NICKNAME_FIELD => 'duplicate',
            self::AVATAR_FIELD => $avatar,
        ]);

        $response = $this->postJson(self::API_METHOD, [
            self::NICKNAME_FIELD => 'duplicate',
            self::AVATAR_FIELD => $avatar,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(self::NICKNAME_FIELD);
    }

    #[Test]
    public function avatarMustBeValidImageAndWithinSizeLimit(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson(self::API_METHOD, [
            self::NICKNAME_FIELD => 'user1',
            self::AVATAR_FIELD => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(self::AVATAR_FIELD);

        $largeFile = UploadedFile::fake()->image('big.jpg')->size(6000);

        $response = $this->postJson(self::API_METHOD, [
            self::NICKNAME_FIELD => 'user2',
            self::AVATAR_FIELD => $largeFile,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(self::AVATAR_FIELD);
    }
}
