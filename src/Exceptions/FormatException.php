<?php

namespace HishabKitab\Payment\Exceptions;

class FormatException
{
    /**
     * Thrown when the instantiated class does not exist.
     *
     * @return FormatException
     */
    public static function forInvalidFormatter(string $class)
    {
        return new static(lang('Format.invalidFormatter', [$class]));
    }

    /**
     * Thrown in Json when the json_encode produces
     * an error code other than JSON_ERROR_NONE and JSON_ERROR_RECURSION.
     *
     * @param string $error
     *
     * @return FormatException
     */
    public static function forInvalidJSON(?string $error = null)
    {
        return new static(lang('Format.invalidJSON', [$error]));
    }

    /**
     * Thrown when the supplied MIME type has no
     * defined Format class.
     *
     * @return FormatException
     */
    public static function forInvalidMime(string $mime)
    {
        return new static(lang('Format.invalidMime', [$mime]));
    }

    /**
     * Thrown on Xml when the `simplexml` extension
     * is not installed.
     *
     * @return FormatException
     *
     * @codeCoverageIgnore
     */
    public static function forMissingExtension()
    {
        return new static(lang('Format.missingExtension'));
    }
}
