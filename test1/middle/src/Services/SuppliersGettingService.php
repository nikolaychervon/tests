<?php

namespace App\Services;

use App\Exceptions\SuppliersListNotFoundException;

class SuppliersGettingService
{
    private const string SUPPLIERS_DB = '/../../suppliers-list.php';

    /**
     * @param string $name
     * @return array
     * @throws \Exception
     */
    public function getSuppliersByName(string $name): array
    {
        $suppliersList = require __DIR__ . self::SUPPLIERS_DB;
        if (!isset($suppliersList[$name])) {
            throw new SuppliersListNotFoundException($name);
        }

        return $suppliersList[$name];
    }
}
