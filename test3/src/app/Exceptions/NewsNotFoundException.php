<?php

namespace App\Exceptions;

class NewsNotFoundException extends BaseException
{
    public function __construct(int $id)
    {
        parent::__construct("News not found with id [{$id}]", 404);
    }
}
