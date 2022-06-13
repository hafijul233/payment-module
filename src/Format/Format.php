<?php

namespace HishabKitab\Payment\Formatter;

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
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->config = config('format');
    }

    /**
     * Returns the current configuration instance.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * A Factory method to return the appropriate formatter for the given mime type.
     *
     */
    public function getFormatter(string $mime): FormatInterface
    {
        if (! array_key_exists($mime, $this->config['formatters'])) {
            throw FormatException::forInvalidMime($mime);
        }

        $className = $this->config['formatters'][$mime];

        if (! class_exists($className)) {
            throw FormatException::forInvalidFormatter($className);
        }

        $class = new $className();

        if (! $class instanceof FormatInterface) {
            throw FormatException::forInvalidFormatter($className);
        }

        return $class;
    }
}
