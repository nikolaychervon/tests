<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UsersListApiTest extends TestCase
{
    use RefreshDatabase;

    private const string NICKNAME_FIELD = 'nickname';
    private const string AVATAR_FIELD = 'avatar';
    private const string API_METHOD_LIST = '/api/list';
    private const string API_METHOD_REGISTER = '/api/register';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    #[Test]
    public function can_list_registered_users()
    {
        $avatars = [
            UploadedFile::fake()->image('a1.jpg')->size(1024),
            UploadedFile::fake()->image('a2.jpg')->size(1024)
        ];

        $this->postJson(self::API_METHOD_REGISTER, [
            self::NICKNAME_FIELD => 'user1',
            self::AVATAR_FIELD => $avatars[0],
        ]);

        $this->postJson(self::API_METHOD_REGISTER, [
            self::NICKNAME_FIELD => 'user2',
            self::AVATAR_FIELD => $avatars[1],
        ]);

        $response = $this->get(self::API_METHOD_LIST);

        $response->assertStatus(200);
        $response->assertSee('user1');
        $response->assertSee('user2');
    }
}
