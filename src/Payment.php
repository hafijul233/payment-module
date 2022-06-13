<?php

namespace HishabKitab\Payment;

use Exception;
use HishabKitab\Payment\Interfaces\VendorInterface;
use Kint\Kint;

class Payment implements VendorInterface
{
    /**
     * @var array|null
     */
    private $config;

    /**
     * @var VendorInterface|null
     */
    private $vendor;

    /**
     * @return VendorInterface|null
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param string $vendor
     * @param $options
     * @throws Exception
     */
    public function setVendor(string $vendor, $options): void
    {
        $className = config("payment.vendors.{$vendor}");
        if ($className == null) {
            throw new Exception("Transaction Vendor not found.");
        }

        $this->vendor = new $className($options);
    }

    /**
     * @return array|null
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

    /**
     * @param string $config
     */
    public function setConfig(string $config = 'payment'): void
    {
        $this->config = config($config);
    }

    /**
     * Payment constructor.
     * @throws Exception
     */
    public function __construct($vendor, $options = [])
    {
        $this->setConfig();
        $this->setVendor($vendor, $options);
    }

    /**
     * provide list of transaction created by that vendor
     *
     * @param array $filters
     * @return array
     */
    public function findMany(array $filters = []): array
    {
        $transactionArray = [];

        Kint::dump($this->getVendor()->findMany());

        return $transactionArray;
    }

    /**
     * return a single transaction detail information
     *
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function findOne(array $transactionInfo = [])
    {
        Kint::dump($this->getVendor()->findOne($transactionInfo = []));
    }

    /**
     * Create or transaction entry on vendor api platform
     *
     * @param array $transactionInfo
     * @return array
     */
    public function createTransaction(array $transactionInfo = []): array
    {
        Kint::dump($this->getVendor()->createTransaction());

        return [];
    }

    /**
     * return a single transaction detail information based on vendor response
     *
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function transactionStatus(array $transactionInfo = [])
    {
        Kint::dump($this->getVendor()->transactionStatus($transactionInfo = []));
    }

    /**
     * send a transaction cancel request to vendor api
     *
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function cancelTransaction(array $transactionInfo = [])
    {
        Kint::dump($this->getVendor()->transactionStatus($transactionInfo = []));
    }

    /**
     * send a transaction refund request to vendor api
     *
     * @param array $transactionInfo
     * @return mixed|void
     */
    public function refundTransaction(array $transactionInfo = [])
    {
        Kint::dump($this->getVendor()->refundTransaction($transactionInfo = []));
    }
}
