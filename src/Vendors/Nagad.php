<?php

namespace HishabKitab\Payment\Vendors;

use HishabKitab\Payment\Abstracts\Vendor;
use HishabKitab\Payment\Interfaces\VendorInterface;

class Nagad extends Vendor implements VendorInterface
{
    /**
     * @param array $filters
     * @return array
     */
    public function findMany(array $filters = []): array
    {
        // TODO: Implement findMany() method.
    }

    /**
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function findOne(array $transactionInfo)
    {
        // TODO: Implement findOne() method.
    }

    /**
     * @param array $inputs
     * @return array
     */
    public function createTransaction(array $inputs = []): array
    {
        // TODO: Implement createTransaction() method.
    }

    /**
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function transactionStatus(array $transactionInfo)
    {
        // TODO: Implement transactionStatus() method.
    }

    /**
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function cancelTransaction(array $transactionInfo)
    {
        // TODO: Implement cancelTransaction() method.
    }

    /**
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function refundTransaction(array $transactionInfo)
    {
        // TODO: Implement refundTransaction() method.
    }
}
