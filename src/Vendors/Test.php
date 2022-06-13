<?php

namespace HishabKitab\Payment\Vendors;

use Exception;
use HishabKitab\Payment\Abstracts\Vendor;
use HishabKitab\Payment\Http\Request;
use HishabKitab\Payment\Interfaces\VendorInterface;

class Test extends Vendor implements VendorInterface
{
    /**
     * @throws Exception
     */
    public function __construct(array $options = [])
    {
        $this->setConfig();
        $this->setMode();
        $this->setBaseURl();
        $this->setClient();
    }

    public function findMany(array $filters = []): array
    {
        return [$this->getClient()
            ->url('/test')
            ->data([])
            ->method(Request::GET)
            ->request(),
        ];
    }

    public function findOne(array $transactionInfo = [])
    {
        return $this->getClient()
            ->url('/test')
            ->data([])
            ->method(Request::GET)
            ->request();
    }

    public function createTransaction(array $transactionInfo = []): array
    {
        return [$this->getClient()
            ->url('/test')
            ->data([])
            ->method(Request::GET)
            ->request(),
        ];
    }

    public function transactionStatus(array $transactionInfo = [])
    {
        return $this->getClient()
            ->url('/test')
            ->data([])
            ->method(Request::GET)
            ->request();
    }

    public function cancelTransaction(array $transactionInfo = [])
    {
        return $this->getClient()
            ->url('/test')
            ->data([])
            ->method(Request::GET)
            ->request();
    }

    public function refundTransaction(array $transactionInfo = [])
    {
        return $this->getClient()
            ->url('/test')
            ->data([])
            ->method(Request::GET)
            ->request();
    }
}
