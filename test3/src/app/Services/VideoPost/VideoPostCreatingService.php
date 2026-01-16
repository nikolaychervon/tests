<?php

namespace App\Services\VideoPost;

use App\Models\VideoPost;
use Illuminate\Http\UploadedFile;

class VideoPostCreatingService
{
    public function create(string $title, UploadedFile $videoFile): VideoPost
    {
        $filepath = $videoFile->store('video-posts', 'public');
        return VideoPost::query()->create(['title' => $title, 'filepath' => $filepath]);
    }
}
