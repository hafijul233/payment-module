<?php


namespace HishabKitab\Payment\Abstracts;


abstract class Config
{
    public $mode = 'sandbox'; // live

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }
}
