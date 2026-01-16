<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ValidationError",
    title: "Validation Error",
    description: "Validation error response",
    required: ["message", "errors"],
    properties: [
        new OA\Property(
            property: "message",
            type: "string",
            example: "Error message example."
        ),
        new OA\Property(
            property: "errors",
            properties: [
                new OA\Property(
                    property: "field1",
                    type: "array",
                    items: new OA\Items(type: "string", example: "Error for field1")
                ),
                new OA\Property(
                    property: "field2",
                    type: "array",
                    items: new OA\Items(type: "string", example: "Error for field2")
                ),
            ],
            type: "object"
        )
    ]
)]
class BaseRequest extends FormRequest
{
}
