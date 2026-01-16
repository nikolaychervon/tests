<?php

namespace App\Http\Requests\VideoPost;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "StoreVideoPost",
    title: "Create Video Post",
    description: "Create Video Post",
    required: ["title", "video"],
    properties: [
        new OA\Property(property: "title", description: "Заголовок", type: "string", example: "Example title"),
        new OA\Property(
            property: "video",
            description: "Видео файл (MP4, AVI, MOV, WMV, MKV, до 100MB)",
            type: "string",
            format: "binary"
        ),
    ]
)]
class StoreVideoPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'video' => 'required|file|mimes:mp4,avi,mov|max:102400',
        ];
    }

    public function messages(): array
    {
        return [
            'video.required' => 'Video file is required',
            'video.file' => 'Must be a valid video file',
            'video.mimes' => 'Video must be one of: mp4, avi, mov, wmv, mkv',
            'video.max' => 'Video must not exceed 100MB',
        ];
    }

    public function getTitle(): string
    {
        return $this->get('title');
    }

    public function getVideo(): UploadedFile
    {
        return $this->file('video');
    }
}
