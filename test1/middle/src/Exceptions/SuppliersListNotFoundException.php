<?php

namespace App\Exceptions;

class SuppliersListNotFoundException extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Cписок поставщиков '$name' не найден.");
    }
}
