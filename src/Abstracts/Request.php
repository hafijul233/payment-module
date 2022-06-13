<?php

namespace HishabKitab\Payment\Abstracts;

use HishabKitab\Payment\Driver\CurlRequest;
use HishabKitab\Payment\Exceptions\HttpException;
use HishabKitab\Payment\Http\Header;
use HishabKitab\Payment\Http\Response;
use HishabKitab\Payment\Interfaces\ResponseInterface;

abstract class Request
{
    public const GET = 'get';

    public const DELETE = 'delete';

    public const HEAD = 'head';

    public const OPTIONS = 'options';

    public const PATCH = 'patch';

    public const POST = 'post';

    public const PUT = 'put';

    /**
     * The response object associated with this request
     *
     * @var ResponseInterface|null
     */
    protected $response = '';

    /**
     * The base URL associated with this request
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * @var string
     */
    protected $url;

    /**
     * The setting values
     *
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     *
     */
    protected $file = null;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $method;

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setHeader(string $name, $value = null): void
    {
        $this->headers[] = new Header($name, $value);
    }

    /**
     * Get a Header Object array
     *
     * @return Header[]
     */
    public function getHeader(): array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method($method);
    }

    /**
     * If the $url is a relative URL, will attempt to create
     * a full URL by prepending $this->baseURI to it.
     *
     * @param string $url
     * @return string
     */
    public function prepareURL(string $url = ''): string
    {
        $this->setUrl($url);

        return $this->getBaseUrl() . $this->getUrl();
    }

    /**
     * @param array $data
     * @return CurlRequest
     */
    public function data(array $data = []): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * set file to bd embedded with request
     *
     * @param null $file
     * @return $this
     */
    public function file($file = null): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * set the url to send api request
     *
     * @param string $url url start with '/' suffix
     * @param bool $append force current url as full url
     * @return $this
     */
    public function url(string $url = '', bool $append = true): self
    {
        if ($append === true) {
            $this->setUrl($url);
        } else {
            $this->setBaseUrl($url);
            $this->setUrl('');
        }

        return $this;
    }

    /**
     * set the request method
     *
     * @param string $method
     * @return $this
     */
    public function method(string $method): self
    {
        switch (strtolower($method)) {
            case self::GET :
            {
                $this->method = self::GET;

                break;
            }

            case self::DELETE :
            {
                $this->method = self::DELETE;

                break;
            }

            case self::HEAD :
            {
                $this->method = self::HEAD;

                break;
            }

            case self::OPTIONS :
            {
                $this->method = self::OPTIONS;

                break;
            }

            case self::PATCH :
            {
                $this->method = self::PATCH;

                break;
            }

            case self::POST :
            {
                $this->method = self::POST;

                break;
            }

            case self::PUT :
            {
                $this->method = self::PUT;

                break;
            }

            default:
            {
                $this->method = self::GET;
            }
        }

        return $this;
    }

    /**
     * Convenience method for sending a GET request.
     *
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function get(array $options = []): Response
    {
        return $this->method(Request::GET)->request($options);
    }

    /**
     * Convenience method for sending a DELETE request.
     *
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function delete(array $options = []): Response
    {
        return $this->method(Request::DELETE)->request($options);
    }

    /**
     * Convenience method for sending a HEAD request.
     *
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function head(array $options = []): Response
    {
        return $this->method(Request::HEAD)->request($options);
    }

    /**
     * Convenience method for sending an OPTIONS request.
     *
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function options(array $options = []): Response
    {
        return $this->method(Request::OPTIONS)->request($options);
    }

    /**
     * Convenience method for sending a PATCH request.
     *
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function patch(array $options = []): Response
    {
        return $this->method(Request::OPTIONS)->request($options);
    }

    /**
     * Convenience method for sending a POST request.
     *
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function post(array $options = []): Response
    {
        return $this->method(Request::POST)->request($options);
    }

    /**
     * Convenience method for sending a PUT request.
     *
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function put(array $options = []): Response
    {
        return $this->method(Request::PUT)->request($options);
    }
}
