<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BaseException",
    title: "BaseException",
    description: "BaseException",
    required: ["success", "message"],
    properties: [
        new OA\Property(property: "success", type: "boolean", example: "false"),
        new OA\Property(property: "message", type: "string", example: "Example error message"),
    ]
)]
class BaseException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], $this->code);
    }
}
