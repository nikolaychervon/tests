<?php

namespace App\Http\Resources\VideoPost;

use App\Models\VideoPost;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "VideoPost",
    title: "Video Post",
    description: "Video Post model",
    required: ["id", "title", "video_link", "created_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Example title"),
        new OA\Property(property: "video_link", type: "string", example: "https://www.youtube.com/watch?v=dQw4w9WgXcQ"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-16 09:21"),
    ]
)]
/** @mixin VideoPost */
class VideoPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'video_link' => asset('storage/' . $this->filepath),
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
