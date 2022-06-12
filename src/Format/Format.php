<?php

namespace HishabKitab\Payment\Formatter;

use HishabKitab\Payment\Config\Format as FormatterConfig;
use HishabKitab\Payment\Exceptions\FormatException;
use HishabKitab\Payment\Interfaces\FormatInterface;

/**
 * The Format class is a convenient place to create Format.
 * Class Format
 * @package HishabKitab\Format
 */
class Format
{
    /**
     * Configuration instance
     *
     * @var FormatterConfig
     */
    protected $config;

    /**
     * Constructor.
     */
    public function __construct(FormatterConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Returns the current configuration instance.
     *
     * @return FormatterConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * A Factory method to return the appropriate formatter for the given mime type.
     *
     * @throws FormatException
     */
    public function getFormatter(string $mime): FormatInterface
    {
        /*if (!array_key_exists($mime, $this->config->formatters)) {
            throw FormatException::forInvalidMime($mime);
        }*/

        $className = $this->config->formatters[$mime];

        /*if (!class_exists($className)) {
            throw FormatException::forInvalidFormatter($className);
        }*/

        $class = new $className();

        /*if (!$class instanceof FormatInterface) {
            throw FormatException::forInvalidFormatter($className);
        }*/

        return $class;
    }
}
