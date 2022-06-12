<?php

namespace HishabKitab\Payment\Vendors;

use HishabKitab\Payment\Abstracts\Vendor;
use HishabKitab\Payment\Driver\CurlRequest;
use HishabKitab\Payment\Interfaces\VendorInterface;

class Test extends Vendor implements VendorInterface
{
    /**
     * @throws \Exception
     */
    public function __construct(array $options = [])
    {
        $this->setConfig(config('test'));
        $this->setMode(($this->config['mode'] ?? 'sandbox'));
        $this->setBaseURI($this->config[$this->getMode()]['endpoint']);
        $this->setClient('curl');
    }

    public function findMany(array $filters = []): array
    {
        /**
         * @var CurlRequest $client
         */
        $client = $this->getClient();
        dd($client->get('/api/test'));
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
