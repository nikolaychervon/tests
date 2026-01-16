<?php

namespace App\Services\VideoPost;

use App\Models\VideoPost;

class VideoPostListService
{
    private const int MAX_PER_PAGE = 20;

    public function getPaginatedList(int $perPage)
    {
        $perPage = min(max(1, $perPage), self::MAX_PER_PAGE);
        return VideoPost::withCursorPagination($perPage);
    }
}
