<?php

namespace App;

use App\Constants\SupplierFieldsConstants;
use App\Exceptions\ImpossibleExactQuantityException;
use App\Exceptions\NotEnoughGoodsException;

class Calculator
{
    private const int MAX_PACK_VALUE = 500;
    private const int MAX_N_VALUE = 10000;
    private const int MAX_OFFERS_COUNT = 1000;
    private const int MAX_PACK_VARIATIONS = 20;

    /**
     * @param array $offers
     * @param int $N
     * @return array
     * @throws ImpossibleExactQuantityException
     * @throws NotEnoughGoodsException
     */
    public function calculate(array $offers, int $N): array
    {
        $this->validate($offers, $N);
        $this->sortOffersByPrice($offers);

        $dp = array_fill(0, $N + 1, PHP_FLOAT_MAX);
        $dp[0] = 0;
        $choice = array_fill(0, $N + 1, null);

        foreach ($offers as $offer) {
            $this->processOffer($offer, $dp, $choice, $N);
            if ($dp[$N] !== PHP_FLOAT_MAX) {
                break;
            }
        }

        if ($dp[$N] === PHP_FLOAT_MAX) {
            throw new ImpossibleExactQuantityException();
        }

        return $this->reconstructChoice($choice, $N);
    }

    /**
     * @param array $offers
     * @param int $N
     * @return void
     * @throws NotEnoughGoodsException
     */
    private function validate(array $offers, int $N): void
    {
        $this->validateInputConstraints($offers, $N);
        $this->validateTotalCount($offers, $N);
        $this->validatePackVariations($offers);
    }

    /**
     * @param array $offers
     * @param int $N
     * @return void
     */
    private function validateInputConstraints(array $offers, int $N): void
    {
        if ($N > self::MAX_N_VALUE) {
            throw new \InvalidArgumentException('N не может быть больше: ' . self::MAX_N_VALUE);
        }

        if (count($offers) > self::MAX_OFFERS_COUNT) {
            throw new \InvalidArgumentException('Кол-во предложений не может быть больше: ' . self::MAX_OFFERS_COUNT);
        }

        foreach ($offers as $offer) {
            $pack = $offer[SupplierFieldsConstants::PACK];
            if ($pack > self::MAX_PACK_VALUE) {
                throw new \InvalidArgumentException('Pack не может превышать: ' . self::MAX_PACK_VALUE);
            }
        }
    }

    /**
     * @param array $optimizedOffers
     * @return void
     */
    private function validatePackVariations(array $optimizedOffers): void
    {
        $uniquePacks = [];
        foreach ($optimizedOffers as $offer) {
            $pack = $offer[SupplierFieldsConstants::PACK];
            $uniquePacks[$pack] = true;
        }

        if (count($uniquePacks) > self::MAX_PACK_VARIATIONS) {
            throw new \InvalidArgumentException(
                'Кол-во вариаций pack превышено. Максимально: ' . self::MAX_PACK_VARIATIONS
            );
        }
    }

    /**
     * @param array $offers
     * @param int $N
     * @return void
     * @throws NotEnoughGoodsException
     */
    private function validateTotalCount(array $offers, int $N): void
    {
        $totalCount = 0;
        foreach ($offers as $offer) {
            $totalCount += $offer[SupplierFieldsConstants::COUNT];
        }

        if ($totalCount < $N) {
            throw new NotEnoughGoodsException();
        }
    }

    /**
     * @param array $offers
     * @return void
     */
    private function sortOffersByPrice(array &$offers): void
    {
        usort($offers, function($a, $b) {
            $priceA = $a[SupplierFieldsConstants::PRICE];
            $priceB = $b[SupplierFieldsConstants::PRICE];
            return $priceA <=> $priceB;
        });
    }

    /**
     * @param array $offer
     * @param array $dp
     * @param array $choice
     * @param int $N
     * @return void
     */
    private function processOffer(array $offer, array &$dp, array &$choice, int $N): void
    {
        [$id, $count, $price, $pack] = $offer;

        $maxPacks = min(
            (int) floor($N / $pack),
            (int) floor($count / $pack),
            self::MAX_PACK_VALUE
        );

        if ($maxPacks <= 0) {
            return;
        }

        for ($currentQty = $N - $pack; $currentQty >= 0; $currentQty--) {
            if ($dp[$currentQty] === PHP_FLOAT_MAX) continue;

            $packsToAdd = min(
                (int) floor(($N - $currentQty) / $pack),
                $maxPacks
            );

            for ($numPacks = 1; $numPacks <= $packsToAdd; $numPacks++) {
                $newQuantity = $currentQty + $numPacks * $pack;
                $newPrice = $dp[$currentQty] + $numPacks * $pack * $price;

                if ($newPrice < $dp[$newQuantity]) {
                    $dp[$newQuantity] = $newPrice;
                    $choice[$newQuantity] = [
                        'id' => $id,
                        'qty' => $numPacks * $pack,
                        'prev' => $currentQty,
                    ];
                }
            }
        }
    }

    /**
     * @param array $choice
     * @param int $target
     * @return array
     */
    private function reconstructChoice(array $choice, int $target): array
    {
        $result = [];
        $current = $target;
        $grouped = [];

        while ($current > 0 && isset($choice[$current])) {
            $decision = $choice[$current];
            $id = $decision['id'];
            $grouped[$id] = ($grouped[$id] ?? 0) + $decision['qty'];
            $current = $decision['prev'];
        }

        foreach ($grouped as $id => $qty) {
            $result[] = [
                'id' => $id,
                'qty' => $qty
            ];
        }

        return $result;
    }
}
