<?php

namespace App;

use App\Constants\SuppliersListConstants;
use App\Services\SuppliersGettingService;

final class App
{
    public function __construct(
        private SuppliersGettingService $suppliersGettingService,
        private Calculator $calculator,
    ) {
    }

    public function start(): void
    {
        try {
            $suppliersList = $this->suppliersGettingService->getSuppliersByName($this->getSuppliersChoice());
            $result = $this->calculator->calculate($suppliersList, $this->getN());
            $this->showResult($result);
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
        }
    }

    /**
     * @return string
     */
    private function getSuppliersChoice(): string
    {
        echo "Выберите список поставщиков:\n";
        foreach (SuppliersListConstants::SUPPLIERS_FOR_SELECT as $index => $supplier) {
            echo ++$index . " - $supplier\n";
        }
        echo "Ваш выбор: ";

        $suppliersChoice = (int) trim(fgets(STDIN));
        if (!in_array($suppliersChoice, range(1, count(SuppliersListConstants::SUPPLIERS_FOR_SELECT)))) {
            $this->showError('Неверный выбор поставщиков!');
            exit;
        }

        return SuppliersListConstants::SUPPLIERS_FOR_SELECT[--$suppliersChoice];
    }

    /**
     * @return int
     */
    private function getN(): int
    {
        echo "Введите число N: ";
        $n = trim(fgets(STDIN));

        if (!is_numeric($n) || $n < 0) {
            $this->showError('Нужно ввести целое положительное число!');
            exit;
        }

        return (int) $n;
    }

    /**
     * @param array $result
     * @return void
     */
    private function showResult(array $result): void
    {
        foreach ($result as $supplierData) {
            echo "У поставщика (id: " . $supplierData['id'] . ") нужно закупить: " . $supplierData['qty'] . " единиц товаров.\n";
        }
    }

    /**
     * @param string $errorMessage
     * @return void
     */
    private function showError(string $errorMessage): void
    {
        echo 'Ошибка: ' . $errorMessage;
    }
}
