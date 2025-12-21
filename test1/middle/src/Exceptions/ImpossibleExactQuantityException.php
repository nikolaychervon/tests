<?php

namespace App\Exceptions;

class ImpossibleExactQuantityException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Невозможно набрать точное количество с учетом ограничений pack и count.');
    }
}
