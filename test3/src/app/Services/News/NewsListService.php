<?php

namespace App\Services\News;

use App\Models\News;

class NewsListService
{
    private const int MAX_PER_PAGE = 20;

    public function getPaginatedList(int $perPage)
    {
        $perPage = min(max(1, $perPage), self::MAX_PER_PAGE);
        return News::withCursorPagination($perPage);
    }
}
