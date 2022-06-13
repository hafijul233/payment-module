<?php

namespace HishabKitab\Payment\Vendors;

use HishabKitab\Payment\Abstracts\Vendor;
use HishabKitab\Payment\Interfaces\VendorInterface;

class SslCommerz extends Vendor implements VendorInterface
{
    /**
     * @inheritDoc
     */
    public function findMany(array $filters = []): array
    {
        // TODO: Implement findMany() method.
    }

    /**
     * @inheritDoc
     */
    public function findOne(array $transactionInfo)
    {
        // TODO: Implement findOne() method.
    }

    /**
     * @inheritDoc
     */
    public function createTransaction(array $inputs = []): array
    {
        // TODO: Implement createTransaction() method.
    }

    /**
     * @inheritDoc
     */
    public function transactionStatus(array $transactionInfo)
    {
        // TODO: Implement transactionStatus() method.
    }

    /**
     * @inheritDoc
     */
    public function cancelTransaction(array $transactionInfo)
    {
        // TODO: Implement cancelTransaction() method.
    }

    /**
     * @inheritDoc
     */
    public function refundTransaction(array $transactionInfo)
    {
        // TODO: Implement refundTransaction() method.
    }
}
