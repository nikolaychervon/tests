<?php

namespace App\Repositories;

use App\Models\News;

class NewsRepository
{
    public function findOne(int $id): News
    {
        return News::query()->findOrFail($id);
    }
}
