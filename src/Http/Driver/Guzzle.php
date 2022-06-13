<?php

namespace HishabKitab\Payment\Http\Driver;

use HishabKitab\Payment\Exceptions\HttpException;
use HishabKitab\Payment\Http\Request;
use HishabKitab\Payment\Http\Response;
use HishabKitab\Payment\Interfaces\RequestInterface;

/**
 * Class Guzzle
 * @package HishabKitab\Payment\Http\Driver
 */
class Guzzle extends Request implements RequestInterface
{
    /**
     * Takes an array of options to set the following possible class properties:
     *
     * @param string $baseurl
     * @param array $options
     */
    public function __construct(string $baseurl, array $options = [])
    {
        $this->config = config('guzzle');

        $this->setBaseUrl($baseurl);
        $this->setOptions($options);

        $this->response = new Response();
    }

    /**
     * Set the HTTP Authentication.
     *
     * @param string $username
     * @param string $password
     * @param string $type basic or digest
     *
     * @return self
     */
    public function setAuth(string $username, string $password, string $type = 'basic'): self
    {
        $this->config['auth'] = [
            $username,
            $password,
            $type,
        ];

        return $this;
    }

    /**
     * Sends an HTTP request to the specified $url. If this is a relative
     * URL, it will be merged with $this->baseUrl to form a complete URL.
     *
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function request(array $options = []): Response
    {
        $url = $this->prepareURL();

        // Reset our curl options so we're on a fresh slate.
        $curlOptions = [];

        $curlOptions[CURLOPT_URL] = $url;
        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $curlOptions[CURLOPT_HEADER] = true;
        $curlOptions[CURLOPT_FRESH_CONNECT] = true;
        // Disable @file uploads in post data.
        $curlOptions[CURLOPT_SAFE_UPLOAD] = true;

        $curlOptions = $this->setGuzzleOptions($curlOptions);
        $curlOptions = $this->applyMethod($curlOptions);
        $curlOptions = $this->applyHeader($curlOptions);

        $ch = curl_init();

        curl_setopt_array($ch, $curlOptions);
        // Send the request and wait for a response.
        $output = curl_exec($ch);
        if ($output === false) {
            throw HttpException::forCurlError((string)curl_errno($ch), curl_error($ch));
        }
        curl_close($ch);

        // Set the string we want to break our response from
        $breakString = "\r\n\r\n";

        if (strpos($output, 'HTTP/1.1 100 Continue') === 0) {
            $output = substr($output, strpos($output, $breakString) + 4);
        }

        // If request and response have Digest
        if (isset($this->config['auth'][2]) && $this->config['auth'][2] === 'digest' && strpos($output, 'WWW-Authenticate: Digest') !== false) {
            $output = substr($output, strpos($output, $breakString) + 4);
        }

        // Split out our headers and body
        $break = strpos($output, $breakString);

        if ($break !== false) {
            // Our headers
            $headers = explode("\n", substr($output, 0, $break));

            $this->setResponseHeaders($headers);

            // Our body
            $body = substr($output, $break + 4);
            $this->setResponseBody($body);
        } else {
            $this->setResponseBody($output);
        }

        return $this->response;
    }

    /**
     * Set CURL options
     *
     * @param array $curlOptions
     * @return array
     * @throws HttpException
     */
    protected function setGuzzleOptions(array $curlOptions = []): array
    {
        // Auth Headers
        if (!empty($this->config['auth'])) {
            $curlOptions[CURLOPT_USERPWD] = $this->config['auth'][0] . ':' . $this->config['auth'][1];

            if (!empty($this->config['auth'][2]) && strtolower($this->config['auth'][2]) === 'digest') {
                $curlOptions[CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
            } else {
                $curlOptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
            }
        }

        // Certificate
        if (!empty($this->config['cert'])) {
            $cert = $this->config['cert'];

            if (is_array($cert)) {
                $curlOptions[CURLOPT_SSLCERTPASSWD] = $cert[1];
                $cert = $cert[0];
            }

            if (!is_file($cert)) {
                throw HttpException::forSSLCertNotFound($cert);
            }

            $curlOptions[CURLOPT_SSLCERT] = $cert;
        }

        // SSL Verification
        if (isset($this->config['verify'])) {
            if (is_string($this->config['verify'])) {
                $file = realpath($this->config['ssl_key']) ?: $this->config['ssl_key'];

                if (!is_file($file)) {
                    throw HttpException::forInvalidSSLKey($this->config['ssl_key']);
                }

                $curlOptions[CURLOPT_CAINFO] = $file;
                $curlOptions[CURLOPT_SSL_VERIFYPEER] = 1;
            } elseif (is_bool($this->config['verify'])) {
                $curlOptions[CURLOPT_SSL_VERIFYPEER] = $this->config['verify'];
            }
        }

        // Debug
        if ($this->config['debug']) {
            $curlOptions[CURLOPT_VERBOSE] = 1;
            $curlOptions[CURLOPT_STDERR] = is_string($this->config['debug']) ? fopen($this->config['debug'], 'a+b') : fopen('php://stderr', 'wb');
        }

        // Allow Redirects
        if ($this->config['allow_redirects'] === true) {
            $settings = $this->config['redirect'];
            $curlOptions[CURLOPT_FOLLOWLOCATION] = 1;
            $curlOptions[CURLOPT_MAXREDIRS] = $settings['max'];

            if ($settings['strict'] === true) {
                $curlOptions[CURLOPT_POSTREDIR] = 1 | 2 | 4;
            }

            $protocols = 0;

            foreach ($settings['protocols'] as $proto) {
                $protocols += constant('CURLPROTO_' . strtoupper($proto));
            }

            $curlOptions[CURLOPT_REDIR_PROTOCOLS] = $protocols;
        } else {
            $curlOptions[CURLOPT_FOLLOWLOCATION] = 0;
        }

        // Timeout
        $curlOptions[CURLOPT_TIMEOUT_MS] = (float)$this->config['timeout'] * 1000;

        // Connection Timeout
        $curlOptions[CURLOPT_CONNECTTIMEOUT_MS] = (float)$this->config['connect_timeout'] * 1000;

        // Post Data - application/x-www-form-urlencoded
        if (!empty($this->config['form_params']) && is_array($this->config['form_params'])) {
            $postFields = http_build_query($this->config['form_params']);
            $curlOptions[CURLOPT_POSTFIELDS] = $postFields;

            // Ensure content-length is set, since CURL doesn't seem to
            // calculate it when HTTPHEADER is set.
            $this->setHeader('Content-Length', (string)strlen($postFields));
            $this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        }

        // Post Data - multipart/form-data
        if (!empty($this->config['multipart']) && is_array($this->config['multipart'])) {
            // setting the POSTFIELDS option automatically sets multipart
            $curlOptions[CURLOPT_POSTFIELDS] = $this->config['multipart'];
        }

        // HTTP Errors
        $curlOptions[CURLOPT_FAILONERROR] = $this->config['http_errors'];

        // JSON
        if (isset($this->config['json'])) {
            $json = json_encode($this->config['json']);
            $this->setBody($json);
            $this->setHeader('Content-Type', 'application/json');
            $this->setHeader('Content-Length', (string)strlen($json));
        }

        // version
        if ($this->config['version'] != null) {
            if ($this->config['version'] == 1.0) {
                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
            } elseif ($this->config['version'] == 1.1) {
                $curlOptions[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
            }
        }

        return $curlOptions;
    }

    /**
     * Apply method
     * @param array $curlOptions
     * @return array
     */
    protected function applyMethod(array $curlOptions): array
    {
        $curlOptions[CURLOPT_CUSTOMREQUEST] = $this->getMethod(true);

        switch ($this->getMethod()) {
            case Request::GET :
            {
                //$this->method = Request::GET;

                break;
            }

            case Request::DELETE :
            {
                //$this->method = Request::DELETE;

                break;
            }

            case Request::HEAD :
            {
                $curlOptions[CURLOPT_NOBODY] = 1;

                break;
            }

            case Request::OPTIONS :
            {
                //$this->method = Request::OPTIONS;

                break;
            }

            case Request::PATCH :
            {
                //$this->method = Request::PATCH;

                break;
            }

            case Request::POST :
            case Request::PUT :
            {
                // See http://tools.ietf.org/html/rfc7230#section-3.3.2
                if ($this->getHeader('content-length') === null && !isset($this->config['multipart'])) {
                    $this->setHeader('Content-Length', '0');
                }

                $this->setHeader('Content-Type', ($this->options['header']['content_type'] ?? '*/*'));

                break;
            }

            default:
            {
                //$this->method = Request::GET;
            }
        }


        $size = strlen($this->body ?? '');

        // Have content?
        if ($size > 0) {
            if (!empty($this->body)) {
                $curlOptions[CURLOPT_POSTFIELDS] = (string)$this->getBody();
            }
        }

        return $curlOptions;
    }

    /**
     * Adds $this->headers to the cURL request.
     * @param array $curlOptions
     * @return array
     */
    protected function applyHeader(array $curlOptions = []): array
    {
        //default headers
        $this->setHeader('Host', parse_url($this->getBaseUrl(), PHP_URL_HOST));
        $this->setHeader('Accept-Encoding', "gzip, deflate, br");
        $this->setHeader('Connection', 'keep-alive');

        //vendor communication headers
        $this->setHeader('Accept', ($this->options['header']['accept_type'] ?? '*/*'));

        //Load All Headers as string
        foreach ($this->getHeader() as $header) {
            $curlOptions[CURLOPT_HTTPHEADER][] = (string)$header;
        }

        return $curlOptions;
    }

    /**
     * Parses the header retrieved from the cURL response into
     * our Response object.
     * @param array $headers
     * @throws HttpException
     */
    protected function setResponseHeaders(array $headers = [])
    {
        foreach ($headers as $header) {
            if (($pos = strpos($header, ':')) !== false) {
                $title = substr($header, 0, $pos);
                $value = substr($header, $pos + 1);

                $this->response->setHeader($title, $value);
            } elseif (strpos($header, 'HTTP') === 0) {
                preg_match('#^HTTP\/([12](?:\.[01])?) (\d+) (.+)#', $header, $matches);

                if (isset($matches[1])) {
                    $this->response->setProtocolVersion($matches[1]);
                }

                if (isset($matches[2])) {
                    $this->response->setStatusCode($matches[2], $matches[3] ?? null);
                }
            }
        }
    }

    protected function setResponseBody($body)
    {
        $mime = 'application/json';

        if ($this->response->hasHeader('Content-Type')) {
            $mime = $this->response->getHeader('Content-Type')->getValueLine();
        }
        if ($mime == Response::JSON) {
            $this->response->setJSON($body);
        } elseif ($mime == Response::XML) {
            $this->response->setXML($body);
        } else {
            $this->response->setBody($body);
        }
    }
}
