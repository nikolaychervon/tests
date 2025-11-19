<?php

namespace App\Extractor;

use App\Enum\GoodFieldsConstants;
use Symfony\Component\DomCrawler\Crawler;

class GoodsExtractor
{
    private const BLOCK_SEPARATOR_CLASS = 'header_tr';
    private const CODE_CLASS = 'code';
    private const NAME_CLASS = 'name';
    private const BRAND_CLASS = 'producer';
    private const LINK_CLASS = 'goodlnk';
    private const STOCK_COUNT_CLASS = 'storehouse span';
    private const GOOD_ID_CLASS = 'input[id^="g"]';
    private const DELIVERY_TIME_CLASS = 'article';

    /**
     * Метод извлекает только нужные нам данные (на сайте есть блоки с "похожими" товарами, которые нам не нужны)
     * Определяется по блоку header_tr
     *
     * @param Crawler $crawler
     * @return Crawler
     */
    public function extractProductRows(Crawler $crawler): Crawler
    {
        $headerRows = $crawler->filter('tr.' . self::BLOCK_SEPARATOR_CLASS);

        if ($headerRows->count() >= 2) {
            return $this->getRowsBetweenHeaders($headerRows->eq(0), $headerRows->eq(1));
        }

        if ($headerRows->count() === 1) {
            return $this->getRowsAfterHeader($headerRows->eq(0));
        }

        return $crawler->filter('tr:not(.' . self::BLOCK_SEPARATOR_CLASS . '):not(.thead)');
    }

    /**
     * @param Crawler $firstHeader
     * @param Crawler $secondHeader
     * @return Crawler
     */
    private function getRowsBetweenHeaders(Crawler $firstHeader, Crawler $secondHeader): Crawler
    {
        $rows = new Crawler();
        $current = $firstHeader->getNode(0)->nextSibling;

        while ($current && $current !== $secondHeader->getNode(0)) {
            if ($current->nodeType === XML_ELEMENT_NODE && $current->nodeName === 'tr') {
                $rows->addNode($current);
            }
            $current = $current->nextSibling;
        }

        return $rows;
    }

    /**
     * @param Crawler $header
     * @return Crawler
     */
    private function getRowsAfterHeader(Crawler $header): Crawler
    {
        $rows = new Crawler();
        $current = $header->getNode(0)->nextSibling;

        while ($current) {
            if ($current->nodeType === XML_ELEMENT_NODE && $current->nodeName === 'tr') {
                $class = $current->getAttribute('class') ?? '';
                if (str_contains($class, self::BLOCK_SEPARATOR_CLASS)) {
                    break;
                }
                $rows->addNode($current);
            }
            $current = $current->nextSibling;
        }

        return $rows;
    }

    /**
     * @param Crawler $firstRow
     * @return array{name: string, code: string, brand: string}
     */
    public function extractCommonData(Crawler $firstRow): array
    {
        return [
            GoodFieldsConstants::NAME => $this->extractProductName($firstRow),
            GoodFieldsConstants::ARTICLE => $this->extractCode($firstRow),
            GoodFieldsConstants::BRAND => $this->extractBrand($firstRow)
        ];
    }

    /**
     * @param Crawler $row
     * @return string
     */
    private function extractProductName(Crawler $row): string
    {
        $filterClass = 'td.' . self::NAME_CLASS;

        $nameNode = $row->filter("$filterClass a." . self::LINK_CLASS);
        if ($nameNode->count() > 0) {
            return trim($nameNode->text());
        }

        $nameNode = $row->filter($filterClass);
        return $nameNode->count() > 0 ? trim($nameNode->text()) : '';
    }

    /**
     * @param Crawler $row
     * @return string
     */
    private function extractCode(Crawler $row): string
    {
        $articleNode = $row->filter('td.' . self::CODE_CLASS);
        return $articleNode->count() > 0 ? trim($articleNode->text()) : '';
    }

    /**
     * @param Crawler $row
     * @return string
     */
    private function extractBrand(Crawler $row): string
    {
        $brandNode = $row->filter('td.' . self::BRAND_CLASS);
        if ($brandNode->count() === 0) {
            return '';
        }

        $brandElement = $brandNode->getNode(0);
        $brandText = '';

        foreach ($brandElement->childNodes as $node) {
            if ($node->nodeType === XML_TEXT_NODE) {
                $brandText = trim($node->textContent);
                break;
            }
        }

        return $brandText;
    }

    /**
     * Так как идет динамическая замена цен через JS, я паршу этот js код и маплю цены по id.
     * По хорошему нужно сделать имитацию браузера и получить html после отработки js, но считаю это оверхед.
     *
     * @param string $html
     * @return array
     */
    public function extractPricesFromJavaScript(string $html): array
    {
        $pricesMap = [];
        preg_match_all("/document\.getElementById\('sp(\d+)'\)\.innerHTML='([^']+)'/", $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $pricesMap[$match[1]] = $match[2];
        }

        return $pricesMap;
    }

    /**
     * @param Crawler $row
     * @return string
     */
    public function extractStockCount(Crawler $row): string
    {
        $countNode = $row->filter('td.' . self::STOCK_COUNT_CLASS);
        return $countNode->count() > 0 ? trim($countNode->text()) : '';
    }

    /**
     * @param Crawler $row
     * @return int
     */
    public function extractDeliveryTime(Crawler $row): int
    {
        $timeNode = $row->filter('td.' . self::DELIVERY_TIME_CLASS);
        if ($timeNode->count() === 0) {
            return 0;
        }

        preg_match('/(\d+)/', trim($timeNode->text()), $matches);
        return isset($matches[1]) ? (int)$matches[1] : 0;
    }

    /**
     * @param Crawler $row
     * @return string
     */
    public function extractProductId(Crawler $row): string
    {
        $idNode = $row->filter(self::GOOD_ID_CLASS);
        return $idNode->count() > 0 ? trim($idNode->attr('value')) : '';
    }
}
