<?php

namespace App\Enum;

class GoodFieldsConstants
{
    public const string
        NAME = 'name',
        PRICE = 'price',
        ARTICLE = 'article',
        BRAND = 'brand',
        COUNT = 'count',
        TIME = 'time',
        ID = 'id';

    public const array REQUIRED_FIELDS = [
        self::NAME,
        self::PRICE,
        self::ARTICLE,
        self::BRAND,
        self::COUNT,
        self::TIME,
        self::ID,
    ];
}
