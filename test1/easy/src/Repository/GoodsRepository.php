<?php

namespace App\Repository;

class GoodsRepository
{
    /**
     * @param string $code
     * @param array $data
     * @return string
     */
    public function saveGoodsList(string $code, array $data): string
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $filename = $this->generateFilename($code);

        if (file_put_contents(__DIR__ . '/../../storage/' . $filename, $json) === false) {
            throw new \RuntimeException("Не удалось сохранить файл: {$filename}");
        }

        return $filename;
    }

    /**
     * @param string $code
     * @return string
     */
    private function generateFilename(string $code): string
    {
        return 'goods_' . $code . '_' . date('Y-m-d_H-i-s') . '.json';
    }
}
