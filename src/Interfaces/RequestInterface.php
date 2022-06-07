<?php

namespace HishabKitab\Payment\Interfaces;

use HishabKitab\Payment\Exceptions\RequestException;

interface RequestInterface
{
    /**
     * Sends an HTTP request to the specified $url. If this is a relative
     * URL, it will be merged with $this->baseURI to form a complete URL.
     *
     * @param string $method
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface;

    /**
     * Convenience method for sending a GET request.
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function get(string $url, array $options = []): ResponseInterface;

    /**
     * Convenience method for sending a DELETE request.
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function delete(string $url, array $options = []): ResponseInterface;

    /**
     * Convenience method for sending a HEAD request.
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function head(string $url, array $options = []): ResponseInterface;

    /**
     * Convenience method for sending an OPTIONS request.
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function options(string $url, array $options = []): ResponseInterface;

    /**
     * Convenience method for sending a PATCH request.
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function patch(string $url, array $options = []): ResponseInterface;

    /**
     * Convenience method for sending a POST request.
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function post(string $url, array $options = []): ResponseInterface;

    /**
     * Convenience method for sending a PUT request.
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     */
    public function put(string $url, array $options = []): ResponseInterface;
}
