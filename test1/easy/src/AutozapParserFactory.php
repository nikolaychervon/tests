<?php

namespace App;

use App\Extractor\GoodsExtractor;
use App\HTTP\HttpClient;
use App\Repository\GoodsRepository;
use App\Validator\CodeValidator;

class AutozapParserFactory
{
    public static function create(): AutozapParser
    {
        return new AutozapParser(
            new HttpClient(),
            new GoodsExtractor(),
            new GoodsRepository(),
            new CodeValidator()
        );
    }
}
