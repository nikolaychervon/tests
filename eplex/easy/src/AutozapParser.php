<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler;

class AutozapParser
{
    public function __construct(
        private HttpClient $httpClient,
        private GoodsExtractor $goodsExtractor,
        private GoodsRepository $goodsRepository,
        private CodeValidator $codeValidator,
    ) {
    }

    /**
     * @param string $code
     * @return array
     */
    public function parse(string $code): array
    {
        try {
            $this->codeValidator->simpleValidate($code);

            $html = $this->httpClient->fetchGoodsListPage($code);
            $crawler = new Crawler($html);

            if ($this->isGoodsListPage($crawler)) {
                $firstGoodUrl = $this->getFirstGoodUrl($crawler);
                $html = $this->httpClient->fetchGoodDetailPage($firstGoodUrl);
                $crawler = new Crawler($html);
            }

            $data = $this->parseGoodsData($crawler);
            if (empty($data)) {
                throw new \RuntimeException("По коду $code ничего не найдено.");
            }

            $filename = $this->goodsRepository->saveGoodsList($code, $data);

            return [
                'success' => true,
                'filename' => $filename,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param Crawler $crawler
     * @return array
     */
    private function parseGoodsData(Crawler $crawler): array
    {
        $rows = $this->goodsExtractor->extractProductRows($crawler);
        if ($rows->count() === 0) {
            return [];
        }

        $commonData = $this->goodsExtractor->extractCommonData($rows->first());
        $pricesMap = $this->goodsExtractor->extractPricesFromJavaScript($crawler->html());

        $result = [];
        foreach ($rows as $index => $row) {
            $rowCrawler = new Crawler($row);
            $item = $this->parseProductRow($rowCrawler, $commonData, $pricesMap, $index + 1);
            if ($item) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @param Crawler $row
     * @param array $commonData
     * @param array $pricesMap
     * @param int $rowNumber
     * @return array|null
     */
    private function parseProductRow(Crawler $row, array $commonData, array $pricesMap, int $rowNumber): ?array
    {
        $item = array_merge($commonData, [
            'price' => $pricesMap[$rowNumber] ?? '',
            'count' => $this->goodsExtractor->extractStockCount($row),
            'time' => $this->goodsExtractor->extractDeliveryTime($row),
            'id' => $this->goodsExtractor->extractProductId($row)
        ]);

        return $this->validateProductData($item) ? $item : null;
    }

    /**
     * @param array $item
     * @return bool
     */
    private function validateProductData(array $item): bool
    {
        $requiredFields = ['name', 'price', 'article', 'brand', 'count', 'time', 'id'];

        foreach ($requiredFields as $field) {
            if (empty($item[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Crawler $crawler
     * @return bool
     */
    private function isGoodsListPage(Crawler $crawler): bool
    {
        return $crawler->filter('td.article a:contains("Цены")')->count() > 0;
    }

    /**
     * @param Crawler $crawler
     * @return string
     */
    private function getFirstGoodUrl(Crawler $crawler): string
    {
        $firstPriceLink = $crawler->filter('td.article a:contains("Цены")')->first();
        return $firstPriceLink->attr('href');
    }
}
