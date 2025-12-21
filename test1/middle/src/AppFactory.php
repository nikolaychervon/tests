<?php

namespace App;

use App\Services\SuppliersGettingService;

class AppFactory
{
    /**
     * @return App
     */
    public static function create(): App
    {
        return new App(
            new SuppliersGettingService(),
            new Calculator(),
        );
    }
}
