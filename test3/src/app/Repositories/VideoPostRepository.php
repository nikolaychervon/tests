<?php

namespace App\Repositories;

use App\Models\VideoPost;

class VideoPostRepository
{
    public function findOne(int $id): VideoPost
    {
        return VideoPost::query()->findOrFail($id);
    }
}
