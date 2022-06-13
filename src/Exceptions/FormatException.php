<?php


namespace HishabKitab\Payment\Exceptions;


use Exception;

class FormatException extends Exception
{
    /**
     * Thrown when the instantiated class does not exist.
     *
     * @param string $class
     * @return FormatException
     */
    public static function forInvalidFormatter(string $class): self
    {
        return new static("\"{$class}\" is not a valid Formatter class.");
    }

    /**
     * Thrown in Json when the json_encode produces
     * an error code other than JSON_ERROR_NONE and JSON_ERROR_RECURSION.
     *
     * @param string|null $error
     *
     * @return FormatException
     */
    public static function forInvalidJSON(string $error = null): self
    {
        return new static("Failed to parse json string, error: \"{$error}\".");
    }

    /**
     * Thrown when the supplied MIME type has no
     * defined Format class.
     *
     * @param string $mime
     * @return FormatException
     */
    public static function forInvalidMime(string $mime): self
    {
        return new static("No Formatter defined for mime type: \"{$mime}\".");

    }

    /**
     * Thrown on Xml when the `simplexml` extension
     * is not installed.
     *
     * @return FormatException
     *
     */
    public static function forMissingExtension(): self
    {
        return new static("The SimpleXML extension is required to format XML.");
    }
}
