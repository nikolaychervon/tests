<?php

namespace App\HTTP;

use GuzzleHttp\Client;

class HttpClient
{
    private const BASE_URL = 'https://www.autozap.ru';
    private const GOODS_LIST = '/goods';
    private const TIMEOUT = 20;

    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => self::TIMEOUT,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            ]
        ]);
    }

    /**
     * Получение данных из списка на главной странице
     *
     * @param string $code
     * @return string
     */
    public function fetchGoodsListPage(string $code): string
    {
        $response = $this->client->post(self::BASE_URL . self::GOODS_LIST, [
            'form_params' => ['code' => $code]
        ]);

        return (string) $response->getBody();
    }

    /**
     * Получение данных с детальной страницы изготовителя и артикула
     *
     * @param string $detailUrl
     * @return string
     */
    public function fetchGoodDetailPage(string $detailUrl): string
    {
        $response = $this->client->get(self::BASE_URL . $detailUrl);
        return (string) $response->getBody();
    }
}
