<?php

namespace HishabKitab\Payment\Interfaces;

interface FormatInterface
{
    /**
     * Takes the given data and formats it.
     *
     * @param array|string $data
     *
     * @return mixed
     */
    public function format($data);
}
