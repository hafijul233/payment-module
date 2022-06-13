<?php

namespace HishabKitab\Payment\Abstracts;

abstract class Vendor
{
    /**
     * @var string
     */
    public $baseUrl;
    /**
     * @var array
     */
    protected $config;
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
    public function setClient(string $client = ''): void
    {
        $paymentConfig = config('payment');

        $className = (isset($paymentConfig['drivers'][$client]))
            ? ($paymentConfig['drivers'][$client] ?? null)
            : ($paymentConfig['drivers'][$paymentConfig['driver']] ?? null);

        if ($className == null) {
            throw new \Exception("Communication driver not found.");
        }

        $this->client = new $className(
            $this->getBaseUrl(),
            $this->getConfig()
        );
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl = ''): void
    {
        $this->baseUrl = (! empty($baseUrl))
            ? $baseUrl
            : $this->config[$this->getMode()]['endpoint'];
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

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
    public function setConfig(array $config = []): void
    {
        if (! empty($config)) {
            $this->config = $config;
        } else {
            $this->config = config('test');
        }
    }

    /**
     * @param string $mode
     */
    public function setMode(string $mode = ''): void
    {
        $this->mode = (! empty($mode))
            ? $mode
            : $this->config['mode'] ?? 'sandbox';
    }
}
