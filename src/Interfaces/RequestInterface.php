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
    public function request($method, string $url, array $options = []): ResponseInterface;

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

    /**
     * Set form data to be sent.
     *
     * @param array $params
     * @param bool $multipart Set TRUE if you are sending CURLFiles
     *
     * @return $this
     */
    public function setForm(array $params, bool $multipart = false);

    /**
     * Get the request method. Overrides the Request class' method
     * since users expect a different answer here.
     *
     * @param bool|false $upper Whether to return in upper or lower case.
     * @return string
     */
    public function getMethod(bool $upper = false): string;

    /**
     * Fires the actual cURL request.
     *
     * @param string $method
     * @param string $url
     * @return ResponseInterface
     * @throws RequestException
     */
    public function send(string $method, string $url): ResponseInterface;

    /**
     * Adds $this->headers to the cURL request.
     * @param array $curlOptions
     * @return array
     */
    public function applyRequestHeaders(array $curlOptions = []): array;

    /**
     * Apply method
     * @param string $method
     * @param array $curlOptions
     * @return array
     */
    public function applyMethod(string $method, array $curlOptions): array;

    /**
     * Apply body
     * @param array $curlOptions
     * @return array
     */
    public function applyBody(array $curlOptions = []): array;

    /**
     * Parses the header retrieved from the cURL response into
     * our Response object.
     * @param array $headers
     */
    public function setResponseHeaders(array $headers = []): void;

    /**
     * Set CURL options
     *
     * @param array $curlOptions
     * @param array $config
     * @return array
     * @throws RequestException
     */
    public function setCURLOptions(array $curlOptions = [], array $config = []): void;

    /**
     * Does the actual work of initializing cURL, setting the options,
     * and grabbing the output.
     *
     * @codeCoverageIgnore
     * @param array $curlOptions
     * @return string
     */
    public function sendRequest(array $curlOptions = []): string;
}
