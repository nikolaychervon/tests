<?php

namespace App\Exceptions;

class VideoPostNotFoundException extends BaseException
{
    public function __construct(int $id)
    {
        parent::__construct("Video post not found with id [{$id}]", 404);
    }
}
