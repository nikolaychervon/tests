<?php

namespace App\Http\Controllers;

use App\DTO\UserDTO;
use App\Http\Requests\UserRegisterRequest;
use App\Services\UserRegisterService;
use App\Services\UsersGettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(
        private UserRegisterService $userRegisterService,
        private UsersGettingService $usersGettingService,
    ) {
    }

    /**
     * @param UserRegisterRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $userDTO = new UserDTO(
            nickname: $request->get('nickname'),
            createdAt: now()->timestamp,
            avatar: $request->file('avatar'),
        );

        $formattedUser = $this->userRegisterService->register($userDTO);
        return response()->json($formattedUser);
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $users = $this->usersGettingService->getList();
        return response()->json($users);
    }
}
