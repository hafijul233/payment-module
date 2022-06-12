<?php

namespace HishabKitab\Payment\Config;

use HishabKitab\Payment\Formatter\Format;
use HishabKitab\Payment\Interfaces\FormatInterface;

class FormatConfig
{
    /**
     * --------------------------------------------------------------------------
     * Available Response Formats
     * --------------------------------------------------------------------------
     *
     * When you perform content negotiation with the request, these are the
     * available formats that your application supports. This is currently
     * only used with the API\ResponseTrait. A valid Format must exist
     * for the specified format.
     *
     * These formats are only checked when the data passed to the respond()
     * method is an array.
     *
     * @var string[]
     */
    public $supportedResponseFormats = [
        'application/json',
        'application/xml', // machine-readable XML
        'text/xml', // human-readable XML
    ];

    /**
     * --------------------------------------------------------------------------
     * Format
     * --------------------------------------------------------------------------
     *
     * Lists the class to use to format responses with of a particular type.
     * For each mime type, list the class that should be used. Format
     * can be retrieved through the getFormatter() method.
     *
     * @var array<string, string>
     */
    public $formatters = [
        'application/json' => JSONFormatter::class,
        'application/xml' => XMLFormatter::class,
        'text/xml' => XMLFormatter::class,
    ];

    /**
     * --------------------------------------------------------------------------
     * Format Options
     * --------------------------------------------------------------------------
     *
     * Additional Options to adjust default formatters behaviour.
     * For each mime type, list the additional options that should be used.
     *
     * @var array<string, int>
     */
    public $formatterOptions = [
        'application/json' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        'application/xml' => 0,
        'text/xml' => 0,
    ];
}
