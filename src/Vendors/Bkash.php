<?php

namespace HishabKitab\Payment\Vendors;

use HishabKitab\Payment\Abstracts\Vendor;
use HishabKitab\Payment\Interfaces\VendorInterface;
use HishabKitab\Payment\Libraries\CurlRequest;

class Bkash extends Vendor implements VendorInterface
{
    /**
     * @var CurlRequest
     * @throw RequestException
     */
    protected $curlRequest;

    public function __construct()
    {
        $this->curlRequest = new CurlRequest([], '');
    }

    public function findMany(array $filters = []): array
    {
        // TODO: Implement findMany() method.
    }

    public function findOne(array $transactionInfo)
    {
        // TODO: Implement findOne() method.
    }

    public function createTransaction(array $inputs = []): array
    {
        // TODO: Implement createTransaction() method.
    }

    public function transactionStatus(array $transactionInfo)
    {
        // TODO: Implement transactionStatus() method.
    }

    public function cancelTransaction(array $transactionInfo)
    {
        // TODO: Implement cancelTransaction() method.
    }

    public function refundTransaction(array $transactionInfo)
    {
        // TODO: Implement refundTransaction() method.
    }
}
