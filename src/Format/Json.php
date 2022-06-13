<?php

namespace HishabKitab\Payment\Formatter;

use HishabKitab\Payment\Exceptions\FormatException;
use HishabKitab\Payment\Interfaces\FormatInterface;

/**
 * JSON data formatter
 */
class Json implements FormatInterface
{
    /**
     * Takes the given data and formats it.
     *
     * @param mixed $data
     *
     * @return bool|string (JSON string | false)
     */
    public function format($data)
    {
        $config = config('format');

        $options = $config['options']['application/json'] ?? JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $options = $options | JSON_PARTIAL_OUTPUT_ON_ERROR;

        $result = json_encode($data, $options);

        if (! in_array(json_last_error(), [JSON_ERROR_NONE, JSON_ERROR_RECURSION], true)) {
            throw FormatException::forInvalidJSON(json_last_error_msg());
        }

        return $result;
    }
}
