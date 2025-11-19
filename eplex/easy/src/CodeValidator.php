<?php

namespace App;

class CodeValidator
{
    /**
     * @param string $code
     * @return void
     */
    public function simpleValidate(string $code): void
    {
        $code = trim(htmlspecialchars($code));

        // Только буквы, цифры, дефисы, точки и слэши
        // Длина от 2 до 20 символов (подробно не знаю)
        if (preg_match('/^[a-zA-Z0-9\-_\.\/]{2,20}$/', $code) !== 1) {
            throw new \Exception('Некорректный артикул товара.');
        };
    }
}
