<?php

namespace App\Services\News;

use App\Models\News;

class NewsCreatingService
{
    public function create(string $title, string $content): News
    {
        return News::query()->create(['title' => $title, 'content' => $content]);
    }
}
