<?php

namespace HishabKitab\Payment\Exceptions;

class RequestException extends \Exception
{
    /**
     * For CurlRequest
     *
     * @return self
     *
     * @codeCoverageIgnore
     */
    public static function forMissingCurl()
    {
        return new static('CURL must be enabled to use the CURLRequest class.');
    }

    /**
     * For CurlRequest
     *
     * @param string $cert
     * @return self
     */
    public static function forSSLCertNotFound(string $cert)
    {
        return new static("SSL certificate not found at: {$cert}");
    }

    /**
     * For CurlRequest
     *
     * @return self
     */
    public static function forInvalidSSLKey(string $key)
    {
        return new static("Cannot set SSL Key. {$key} is not a valid file.");
    }

    /**
     * For CurlRequest
     *
     * @return self
     *
     * @codeCoverageIgnore
     */
    public static function forCurlError(string $errorNum, string $error)
    {
        return new static("CURL Error {$errorNum} : {$error}.");
    }

    /**
     * For IncomingRequest
     *
     * @return self
     */
    public static function forInvalidNegotiationType(string $type)
    {
        return new static(lang('HTTP.invalidNegotiationType', [$type]));
    }

    /**
     * For Message
     *
     * @return self
     */
    public static function forInvalidHTTPProtocol(string $protocols)
    {
        return new static(lang('HTTP.invalidHTTPProtocol', [$protocols]));
    }

    /**
     * For Negotiate
     *
     * @return self
     */
    public static function forEmptySupportedNegotiations()
    {
        return new static(lang('HTTP.emptySupportedNegotiations'));
    }

    /**
     * For RedirectResponse
     *
     * @return self
     */
    public static function forInvalidRedirectRoute(string $route)
    {
        return new static(lang('HTTP.invalidRoute', [$route]));
    }

    /**
     * For Response
     *
     * @return self
     */
    public static function forMissingResponseStatus()
    {
        return new static(lang('HTTP.missingResponseStatus'));
    }

    /**
     * For Response
     *
     * @return self
     */
    public static function forInvalidStatusCode(int $code)
    {
        return new static(lang('HTTP.invalidStatusCode', [$code]));
    }

    /**
     * For Response
     *
     * @return self
     */
    public static function forUnkownStatusCode(int $code)
    {
        return new static(lang('HTTP.unknownStatusCode', [$code]));
    }

    /**
     * For URI
     *
     * @return self
     */
    public static function forUnableToParseURI(string $uri)
    {
        return new static(lang('HTTP.cannotParseURI', [$uri]));
    }

    /**
     * For URI
     *
     * @return self
     */
    public static function forURISegmentOutOfRange(int $segment)
    {
        return new static(lang('HTTP.segmentOutOfRange', [$segment]));
    }

    /**
     * For URI
     *
     * @return self
     */
    public static function forInvalidPort(int $port)
    {
        return new static(lang('HTTP.invalidPort', [$port]));
    }

    /**
     * For URI
     *
     * @return self
     */
    public static function forMalformedQueryString()
    {
        return new static(lang('HTTP.malformedQueryString'));
    }

    /**
     * For Uploaded file move
     *
     * @return self
     */
    public static function forAlreadyMoved()
    {
        return new static(lang('HTTP.alreadyMoved'));
    }

    /**
     * For Uploaded file move
     *
     * @return self
     */
    public static function forInvalidFile(?string $path = null)
    {
        return new static(lang('HTTP.invalidFile'));
    }

    /**
     * For Uploaded file move
     *
     * @return self
     */
    public static function forMoveFailed(string $source, string $target, string $error)
    {
        return new static(lang('HTTP.moveFailed', [$source, $target, $error]));
    }

    /**
     * For Invalid SameSite attribute setting
     *
     * @return self
     *
     * @deprecated Use `CookieException::forInvalidSameSite()` instead.
     *
     * @codeCoverageIgnore
     */
    public static function forInvalidSameSiteSetting(string $samesite)
    {
        return new static(lang('Security.invalidSameSiteSetting', [$samesite]));
    }
}
