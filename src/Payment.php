<?php

namespace HishabKitab\Payment;

use HishabKitab\Payment\Interfaces\VendorInterface;

class Payment
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
     * @throws \Exception
     */
    public function setVendor(string $vendor, $options): void
    {
        $className = config("payment.vendors.{$vendor}");
        if ($className == null) {
            throw new \Exception("Transaction Vendor not found.");
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
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Payment constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->setConfig(config('payment'));
        $this->setVendor('bkash', $options = []);
        dd($this->getVendor()->findMany());
    }
}
