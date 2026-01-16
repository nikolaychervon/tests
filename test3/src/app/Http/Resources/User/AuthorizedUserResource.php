<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AuthorizedUser",
    title: "Authorized User",
    description: "Authorized user data",
    properties: [
        new OA\Property(property: "success", type: "boolean", example: true),
        new OA\Property(
            property: "data",
            properties: [
                new OA\Property(property: "user", ref: "#/components/schemas/User"),
                new OA\Property(property: "token", type: "string", example: "1|TOKEN"),
                new OA\Property(property: "token_type", type: "string", example: "Bearer")
            ],
            type: "object"
        ),
        new OA\Property(property: "message", type: "string", example: "User registered successfully")
    ]
)]
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
