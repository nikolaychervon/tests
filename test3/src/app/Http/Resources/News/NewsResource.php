<?php

namespace App\Http\Resources\News;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "News",
    title: "News",
    description: "News model",
    required: ["id", "title", "content", "created_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Example title"),
        new OA\Property(property: "content", type: "string", example: "Example content"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-16 09:21"),
    ]
)]
/** @mixin News */
class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
