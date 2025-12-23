<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Repositories\UserRedisRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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
    public function can_register_user_with_valid_data()
    {
        $avatar = UploadedFile::fake()->image('avatar.jpg')->size(1024);

        $response = $this->postJson(self::API_METHOD, [
            self::NICKNAME_FIELD => self::BASE_NICKNAME,
            self::AVATAR_FIELD => $avatar,
        ]);

        $response->assertStatus(200);
        Storage::disk('public')->assertExists('avatars/' . $avatar->hashName());

        $users = Cache::get(UserRedisRepository::KEY);
        $this->assertNotNull($users[self::BASE_NICKNAME]);
        $this->assertEquals($users[self::BASE_NICKNAME][self::NICKNAME_FIELD], self::BASE_NICKNAME);
        $this->assertEquals($users[self::BASE_NICKNAME][self::AVATAR_PATH_FIELD], 'avatars/' . $avatar->hashName());
    }

    #[Test]
    public function cannot_register_user_with_void_data()
    {
        $response = $this->postJson(self::API_METHOD);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannot_register_user_with_duplicate_nickname()
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
    public function avatar_must_be_valid_image_and_within_size_limit()
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
