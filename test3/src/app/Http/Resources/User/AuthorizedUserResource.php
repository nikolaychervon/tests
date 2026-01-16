<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class AuthorizedUserResource extends JsonResource
{
    private string $token;

    public function __construct(User $resource, string $token)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this),
            'token' => $this->token,
            'token_type' => 'Bearer'
        ];
    }
}
