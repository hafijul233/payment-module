<?php

namespace HishabKitab\Payment\Abstracts;

use HishabKitab\Payment\Exceptions\HttpException;
use HishabKitab\Payment\Interfaces\ResponseInterface;
use HishabKitab\Payment\Libraries\URI;

abstract class Request
{
    /**
     * The response object associated with this request
     *
     * @var ResponseInterface|null
     */
    protected $response;

    /**
     * The URI associated with this request
     *
     * @var URI
     */
    protected $baseURI;

    /**
     * The setting values
     *
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $method;

    /**
     *
     */
    protected $file;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setHeader(string $name, $value = null): void
    {
        $this->headers[$name] = strval($value);
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->headers;
    }

    /**
     * Get the request method
     *
     * @param bool|false $upper Whether to return in upper or lower case.
     * @return string
     */
    public function getMethod(bool $upper = false): string
    {
        return ($upper) ? strtoupper($this->method) : strtolower($this->method);
    }

    /**
     * If the $url is a relative URL, will attempt to create
     * a full URL by prepending $this->baseURI to it.
     * @param string $url
     * @return string
     * @throws HttpException
     */
    public function prepareURL(string $url): string
    {
        // If it's a full URI, then we have nothing to do here...
        if (strpos($url, '://') !== false) {
            return $url;
        }

        $uri = $this->baseURI->resolveRelativeURI($url);

        // Create the string instead of casting to prevent baseURL muddling
        return URI::createURIString($uri->getScheme(), $uri->getAuthority(), $uri->getPath(), $uri->getQuery(), $uri->getFragment());
    }
}
