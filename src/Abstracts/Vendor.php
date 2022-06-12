<?php

namespace HishabKitab\Payment\Abstracts;

use HishabKitab\Payment\Libraries\URI;

abstract class Vendor
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var URI
     */
    public $baseURI;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string [sandbox|live]
     */
    protected $mode;

    /**
     * @var
     */
    protected $client;

    /**
     * @return array
     */
    public function getConfig(): array
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
     * @return URI
     */
    public function getBaseURI(): URI
    {
        return $this->baseURI;
    }

    /**
     * @param string $baseURI
     */
    public function setBaseURI(string $baseURI): void
    {
        $this->baseURI = new URI($baseURI);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param string $mode
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     * @throws \Exception
     */
    public function setClient(string $client): void
    {
        $className = config("payment.drivers.{$client}");

        if ($className == null) {
            throw new \Exception("Communication driver not found.");
        }
        $this->client = new $className($this->getBaseURI());
    }
}
