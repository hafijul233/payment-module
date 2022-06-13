<?php

namespace HishabKitab\Payment\Exceptions;

use Exception;

class HttpException extends Exception
{
    /**
     * @return static
     */
    public static function forMissingCurl(): self
    {
        return new static("CURL must be enabled to use the CURLRequest class.");
    }

    /**
     * @param string $cert
     * @return static
     */
    public static function forSSLCertNotFound(string $cert): self
    {
        return new static("SSL certificate not found at: {$cert}");
    }

    /**
     * @param string $key
     * @return static
     */
    public static function forInvalidSSLKey(string $key): self
    {
        return new static("Cannot set SSL Key. {$key} is not a valid file.");
    }

    /**
     * @param string $errorNum
     * @param string $error
     * @return static
     */
    public static function forCurlError(string $errorNum, string $error): self
    {
        return new static("CURL Error {$errorNum} : {$error}");
    }

    /**
     * @param string $type
     * @return static
     */
    public static function forInvalidNegotiationType(string $type): self
    {
        return new static("{$type} is not a valid negotiation type. Must be one of: media, charset, encoding, language.");
    }

    /**
     * @param string $protocols
     * @return static
     */
    public static function forInvalidHTTPProtocol(string $protocols): self
    {
        return new static("Invalid HTTP Protocol Version. Must be one of: {$protocols}");
    }

    /**
     * @return static
     */
    public static function forEmptySupportedNegotiations(): self
    {
        return new static("You must provide an array of supported values to all Negotiations.");
    }

    /**
     * @param string $route
     * @return static
     */
    public static function forInvalidRedirectRoute(string $route): self
    {
        return new static("{$route} route cannot be found while reverse-routing.");
    }

    /**
     * @return static
     */
    public static function forMissingResponseStatus(): self
    {
        return new static("HTTP Response is missing a status code");
    }

    /**
     * @param int $code
     * @return static
     */
    public static function forInvalidStatusCode(int $code): self
    {
        return new static("{$code} is not a valid HTTP return status code");
    }

    /**
     * @param int $code
     * @return static
     */
    public static function forUnkownStatusCode(int $code): self
    {
        return new static("Unknown HTTP status code provided with no message: {$code}");
    }

    /**
     * @param string $uri
     * @return static
     */
    public static function forUnableToParseURI(string $uri): self
    {
        return new static("Unable to parse URI: {$uri}");
    }

    /**
     * @param int $segment
     * @return static
     */
    public static function forURISegmentOutOfRange(int $segment): self
    {
        return new static("Request URI segment is out of range: {$segment}");
    }

    /**
     * @param int $port
     * @return static
     */
    public static function forInvalidPort(int $port): self
    {
        return new static("Ports must be between 0 and 65535. Given: {$port}");
    }

    /**
     * @return static
     */
    public static function forMalformedQueryString(): self
    {
        return new static("Query strings may not include URI fragments.");
    }

    /**
     * @return static
     */
    public static function forAlreadyMoved(): self
    {
        return new static("The uploaded file has already been moved.");
    }

    /**
     * @param string|null $path
     * @return static
     */
    public static function forInvalidFile(?string $path = null): self
    {
        return new static("The original file is not a valid file.");
    }

    /**
     * @param string $source
     * @param string $target
     * @param string $error
     * @return static
     */
    public static function forMoveFailed(string $source, string $target, string $error): self
    {
        return new static("Could not move file {$source} to {$target} ({$error})");
    }
}
