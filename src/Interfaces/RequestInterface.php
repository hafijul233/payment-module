<?php

namespace HishabKitab\Payment\Interfaces;

use HishabKitab\Payment\Http\Response;

interface RequestInterface
{
    /**
     * Convenience method for sending a GET request.
     * @param array $data
     * @return self
     */
    public function data(array $data = []);

    /**
     * Convenience method for sending a GET request.
     * @param array|null $file
     * @return self
     */
    public function file($file = null);

    /**
     * Convenience method for sending a GET request.
     * @param string $url
     * @param array $options
     * @return Response
     */
    public function get(string $url, array $options = []): Response;

    /**
     * Convenience method for sending a DELETE request.
     * @param string $url
     * @param array $options
     * @return Response
     */
    public function delete(string $url, array $options = []): Response;

    /**
     * Convenience method for sending a HEAD request.
     * @param string $url
     * @param array $options
     * @return Response
     */
    public function head(string $url, array $options = []): Response;

    /**
     * Convenience method for sending an OPTIONS request.
     * @param string $url
     * @param array $options
     * @return Response
     */
    public function options(string $url, array $options = []): Response;

    /**
     * Convenience method for sending a PATCH request.
     * @param string $url
     * @param array $options
     * @return Response
     */
    public function patch(string $url, array $options = []): Response;

    /**
     * Convenience method for sending a POST request.
     * @param string $url
     * @param array $options
     * @return Response
     */
    public function post(string $url, array $options = []): Response;

    /**
     * Convenience method for sending a PUT request.
     * @param string $url
     * @param array $options
     * @return Response
     */
    public function put(string $url, array $options = []): Response;
}
