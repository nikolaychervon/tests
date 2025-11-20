<?php

use App\AppFactory;

require_once 'vendor/autoload.php';

$app = AppFactory::create();
$app->start();
