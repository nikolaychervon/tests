<?php

namespace App\Services\VideoPost;

use App\Exceptions\VideoPostNotFoundException;
use App\Models\VideoPost;
use App\Repositories\VideoPostRepository;

class VideoPostShowService
{
    public function __construct(private VideoPostRepository $videoPostRepository)
    {
    }

    public function get(int $id): VideoPost
    {
        try {
            return $this->videoPostRepository->findOne($id);
        } catch (\Exception $e) {
            throw new VideoPostNotFoundException($id);
        }
    }
}
