<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Resources\User\AuthorizedUserResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\User\UserLoginService;
use App\Services\User\UserRegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Authentication",
    description: "Аутентификация пользователей"
)]
class AuthController extends Controller
{
    public function __construct(
        private UserRegisterService $userRegisterService,
        private UserLoginService $userLoginService
    ) {
    }

    #[OA\Post(
        path: "/auth/register",
        description: "Создает нового пользователя и возвращает токен",
        summary: "Регистрация нового пользователя",
        requestBody: new OA\RequestBody(
            description: "Данные для регистрации",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/Auth")
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Успешная регистрация",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "success",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "user",
                                    ref: "#/components/schemas/User"
                                ),
                                new OA\Property(
                                    property: "token",
                                    type: "string",
                                    example: "1|TOKENTESTESTSETESTSETSETSETSETSET"
                                ),
                                new OA\Property(
                                    property: "token_type",
                                    type: "string",
                                    example: "Bearer"
                                )
                            ],
                            type: "object"
                        ),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "User registered successfully"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")
            )
        ]
    )]
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = $this->userRegisterService->createUser($request->getLogin(), $request->getPassword());
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => new AuthorizedUserResource($user, $token),
            'message' => 'User registered successfully',
        ], 201);
    }

    #[OA\Post(
        path: "/auth/login",
        description: "Авторизация пользователя",
        summary: "Авторизация пользователя",
        requestBody: new OA\RequestBody(
            description: "Данные для входа",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/Auth")
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Успешная авторизация",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "success",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "user",
                                    ref: "#/components/schemas/User"
                                ),
                                new OA\Property(
                                    property: "token",
                                    type: "string",
                                    example: "1|TOKENTESTESTSETESTSETSETSETSETSET"
                                ),
                                new OA\Property(
                                    property: "token_type",
                                    type: "string",
                                    example: "Bearer"
                                )
                            ],
                            type: "object"
                        ),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Logged in successfully"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")
            )
        ]
    )]
    public function login(LoginUserRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->userLoginService->login($request->getLogin(), $request->getPassword());
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => new AuthorizedUserResource($user, $token),
            'message' => 'Logged in successfully',
        ], 201);
    }

    #[OA\Post(
        path: "/auth/logout",
        description: "Отзыв текущего токена аутентификации",
        summary: "Выход из системы",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный выход",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "success",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Logged out successfully"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Не авторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Unauthenticated."
                        )
                    ]
                )
            )
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    #[OA\Get(
        path: "/auth/user",
        description: "Возвращает данные аутентифицированного пользователя",
        summary: "Получить информацию о текущем пользователе",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешно",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "success",
                            type: "boolean",
                            example: true
                        ),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "user",
                                    ref: "#/components/schemas/User"
                                )
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Не авторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Unauthenticated."
                        )
                    ]
                )
            )
        ]
    )]
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
            ],
        ]);
    }
}
