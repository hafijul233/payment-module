<?php

namespace HishabKitab\Payment\Formatter;

use HishabKitab\Payment\Config\Format as FormatterConfig;
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
        $config = new FormatterConfig();

        $options = $config->formatterOptions['application/json'] ?? JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $options = $options | JSON_PARTIAL_OUTPUT_ON_ERROR;

        //$options = ENVIRONMENT === 'production' ? $options : $options | JSON_PRETTY_PRINT;

        $result = json_encode($data, $options, 512);

        /*if (! in_array(json_last_error(), [JSON_ERROR_NONE, JSON_ERROR_RECURSION], true)) {
            throw FormatException::forInvalidJSON(json_last_error_msg());
        }*/

        return $result;
    }
}
