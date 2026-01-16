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

class AuthController extends Controller
{
    public function __construct(
        private UserRegisterService $userRegisterService,
        private UserLoginService $userLoginService
    ) {
    }

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

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

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
