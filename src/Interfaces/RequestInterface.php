<?php

namespace HishabKitab\Payment\Interfaces;

use HishabKitab\Payment\Http\Response;

interface RequestInterface
{
    /**
     * attach array data to form request.
     *
     * @param array $data
     * @return self
     */
    public function data(array $data = []);

    /**
     * attach file object request
     *
     * @param array|null $file
     * @return self
     */
    public function file($file = null);

    /**
     * set the url to send api request
     *
     * @param string $url url start with '/' suffix
     * @param bool $append force current url as full url
     * @return $this
     */
    public function url(string $url = '', bool $append = true);

    /**
     * set the request method
     *
     * @param string $method
     * @return $this
     */
    public function method(string $method);

    /**
     * Convenience method for sending a request.
     *
     * @param array $options
     * @return Response
     */
    public function request(array $options = []): Response;

    /**
     * Convenience method for sending a GET request.
     *
     * @param array $options
     * @return Response
     */
    public function get(array $options = []): Response;

    /**
     * Convenience method for sending a DELETE request.
     *
     * @param array $options
     * @return Response
     */
    public function delete(array $options = []): Response;

    /**
     * Convenience method for sending a HEAD request.
     *
     * @param array $options
     * @return Response
     */
    public function head(array $options = []): Response;

    /**
     * Convenience method for sending an OPTIONS request.
     *
     * @param array $options
     * @return Response
     */
    public function options(array $options = []): Response;

    /**
     * Convenience method for sending a PATCH request.
     *
     * @param array $options
     * @return Response
     */
    public function patch(array $options = []): Response;

    /**
     * Convenience method for sending a POST request.
     * @param array $options
     * @return Response
     */
    public function post(array $options = []): Response;

    /**
     * Convenience method for sending a PUT request.
     *
     * @param array $options
     * @return Response
     */
    public function put(array $options = []): Response;
}
