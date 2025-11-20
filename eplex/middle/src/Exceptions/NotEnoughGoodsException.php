<?php

namespace App\Exceptions;

class NotEnoughGoodsException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Недостаточно общего количества товара.');
    }
}
