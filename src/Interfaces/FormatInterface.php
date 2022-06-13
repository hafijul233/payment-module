<?php


namespace HishabKitab\Payment\Interfaces;


interface FormatInterface
{
    /**
     * Takes the given data and formats it.
     *
     * @param mixed $data
     *
     * @return bool|string
     */
    public function format($data);
}
