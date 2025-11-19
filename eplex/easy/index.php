<?php

require_once 'vendor/autoload.php';

use App\AutozapParser;
use App\CodeValidator;
use App\GoodsExtractor;
use App\GoodsRepository;
use App\HttpClient;

if (PHP_SAPI !== 'cli') {
    die('Этот скрипт предназначен только для командной строки');
}

if (!isset($argv[1])) {
    echo "Введите артикул товара: ";
    $code = trim(fgets(STDIN));

    if (empty($code)) {
        echo "Ошибка: артикул не может быть пустым\n";
        exit(1);
    }
} else {
    $code = $argv[1];
}

$client = new HttpClient();
$extractor = new GoodsExtractor();
$repository = new GoodsRepository();
$validator = new CodeValidator();
$parser = new AutozapParser($client, $extractor, $repository, $validator);
$result = $parser->parse($code);

if ($result['success']) {
    echo 'Файл с данными успешно сохранен. Filename: ' . $result['filename'];
} else {
    echo "Ошибка: " . $result['error'] . "\n";
    exit(1);
}
