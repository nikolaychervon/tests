<?php

namespace App\Services\News;

use App\Exceptions\NewsNotFoundException;
use App\Models\News;
use App\Repositories\NewsRepository;

class NewsShowService
{
    public function __construct(private NewsRepository $newsRepository)
    {
    }

    public function get(int $id): News
    {
        try {
            return $this->newsRepository->findOne($id);
        } catch (\Exception $e) {
            throw new NewsNotFoundException($id);
        }
    }
}
