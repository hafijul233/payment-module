<?php

namespace HishabKitab\Payment\Driver;

use HishabKitab\Payment\Abstracts\Request;
use HishabKitab\Payment\Exceptions\HttpException;
use HishabKitab\Payment\Http\Response;
use HishabKitab\Payment\Interfaces\RequestInterface;
use HishabKitab\Payment\Libraries\URI;

/**
 * Class CurlRequest
 * @package HishabKitab\Payment\Libraries
 */
class CurlRequest extends Request implements RequestInterface
{
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
     * Set form data to be sent.
     *
     * @param array $params
     * @param bool $multipart Set TRUE if you are sending CURLFiles
     *
     * @return $this
     */
    public function setForm(array $params, bool $multipart = false): self
    {
        if ($multipart) {
            $this->config['multipart'] = $params;
        } else {
            $this->config['form_params'] = $params;
        }

        return $this;
    }

    /**
     * Set JSON data to be sent.
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function setJSON($data): self
    {
        $this->config['json'] = $data;

        return $this;
    }

    /**
     * Sets the correct settings based on the options array
     * passed in.
     * @param array $options
     * @throws HttpException
     */
    protected function parseOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->config[$key] = $value;
        }
    }

    /**
     * Adds $this->headers to the cURL request.
     * @param array $curlOptions
     * @return array
     */
    protected function applyRequestHeaders(array $curlOptions = []): array
    {
        if (empty($this->headers)) {
            return $curlOptions;
        }

        $set = [];

        foreach (array_keys($this->headers) as $name) {
            $set[] = $name . ': ' . $this->getHeaderLine($name);
        }

        $curlOptions[CURLOPT_HTTPHEADER] = $set;

        return $curlOptions;
    }

    /**
     * Apply method
     * @param string $method
     * @param array $curlOptions
     * @return array
     */
    protected function applyMethod(string $method, array $curlOptions): array
    {
        $method = strtoupper($method);

        $this->method = $method;
        $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;

        $size = strlen($this->body ?? '');

        // Have content?
        if ($size > 0) {
            return $this->applyBody($curlOptions);
        }

        if ($method === 'PUT' || $method === 'POST') {
            // See http://tools.ietf.org/html/rfc7230#section-3.3.2
            if ($this->header('content-length') === null && ! isset($this->config['multipart'])) {
                $this->setHeader('Content-Length', '0');
            }
        } elseif ($method === 'HEAD') {
            $curlOptions[CURLOPT_NOBODY] = 1;
        }

        return $curlOptions;
    }

    /**
     * Apply body
     * @param array $curlOptions
     * @return array
     */
    protected function applyBody(array $curlOptions = []): array
    {
        if (! empty($this->body)) {
            $curlOptions[CURLOPT_POSTFIELDS] = (string)$this->getBody();
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

    /**
     * Set CURL options
     *
     * @param array $curlOptions
     * @param array $config
     * @return array
     * @throws HttpException
     */
    protected function setCURLOptions(array $curlOptions = [], array $config = []): array
    {
        // Auth Headers
        if (! empty($config['auth'])) {
            $curlOptions[CURLOPT_USERPWD] = $config['auth'][0] . ':' . $config['auth'][1];

            if (! empty($config['auth'][2]) && strtolower($config['auth'][2]) === 'digest') {
                $curlOptions[CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
            } else {
                $curlOptions[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
            }
        }

        // Certificate
        if (! empty($config['cert'])) {
            $cert = $config['cert'];

            if (is_array($cert)) {
                $curlOptions[CURLOPT_SSLCERTPASSWD] = $cert[1];
                $cert = $cert[0];
            }

            if (! is_file($cert)) {
                throw HttpException::forSSLCertNotFound($cert);
            }

            $curlOptions[CURLOPT_SSLCERT] = $cert;
        }

        // SSL Verification
        if (isset($config['verify'])) {
            if (is_string($config['verify'])) {
                $file = realpath($config['ssl_key']) ?: $config['ssl_key'];

                if (! is_file($file)) {
                    throw HttpException::forInvalidSSLKey($config['ssl_key']);
                }

                $curlOptions[CURLOPT_CAINFO] = $file;
                $curlOptions[CURLOPT_SSL_VERIFYPEER] = 1;
            } elseif (is_bool($config['verify'])) {
                $curlOptions[CURLOPT_SSL_VERIFYPEER] = $config['verify'];
            }
        }

        // Debug
        if ($this->config['debug']) {
            $curlOptions[CURLOPT_VERBOSE] = 1;
            $curlOptions[CURLOPT_STDERR] = is_string($this->config['debug']) ? fopen($this->config['debug'], 'a+b') : fopen('php://stderr', 'wb');
        }

        /*        // Decode Content
                if (! empty($config['decode_content'])) {
                    $accept = $this->getHeaderLine('Accept-Encoding');

                    if ($accept) {
                        $curlOptions[CURLOPT_ENCODING] = $accept;
                    } else {
                        $curlOptions[CURLOPT_ENCODING] = '';
                        $curlOptions[CURLOPT_HTTPHEADER] = 'Accept-Encoding';
                    }
                }*/

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
        if (! empty($config['form_params']) && is_array($config['form_params'])) {
            $postFields = http_build_query($config['form_params']);
            $curlOptions[CURLOPT_POSTFIELDS] = $postFields;

            // Ensure content-length is set, since CURL doesn't seem to
            // calculate it when HTTPHEADER is set.
            $this->setHeader('Content-Length', (string)strlen($postFields));
            $this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        }

        // Post Data - multipart/form-data
        if (! empty($config['multipart']) && is_array($config['multipart'])) {
            // setting the POSTFIELDS option automatically sets multipart
            $curlOptions[CURLOPT_POSTFIELDS] = $config['multipart'];
        }

        // HTTP Errors
        $curlOptions[CURLOPT_FAILONERROR] = $this->config['http_errors'];

        // JSON
        if (isset($config['json'])) {
            // Will be set as the body in `applyBody()`
            $json = json_encode($config['json']);
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
     * Does the actual work of initializing cURL, setting the options,
     * and grabbing the output.
     *
     */
    protected function sendRequest(array $curlOptions = []): string
    {
        $ch = curl_init();

        curl_setopt_array($ch, $curlOptions);

        // Send the request and wait for a response.
        $output = curl_exec($ch);

        if ($output === false) {
            throw HttpException::forCurlError((string)curl_errno($ch), curl_error($ch));
        }

        curl_close($ch);

        return $output;
    }

    /**
     * Takes an array of options to set the following possible class properties:
     *
     * @param string $baseURI
     * @param array $options
     * @throws HttpException
     */
    public function __construct($baseURI = '', array $options = [])
    {
        if (! function_exists('curl_version')) {
            throw HttpException::forMissingCurl();
        }

        $this->response = new Response();
        $this->baseURI = ($baseURI instanceof URI) ? $baseURI : new URI($baseURI);
        $this->config = config('curl');
        $this->parseOptions($options);
    }

    /**
     * Sends an HTTP request to the specified $url. If this is a relative
     * URL, it will be merged with $this->baseURI to form a complete URL.
     *
     * @param string $method
     * @param string $url
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    protected function request(string $method, string $url, array $options = []): Response
    {
        $this->parseOptions($options);

        $url = $this->prepareURL($url);

        // Reset our curl options so we're on a fresh slate.
        $curlOptions = [];

        if (! empty($this->config['query']) && is_array($this->config['query'])) {
            // This is likely too naive a solution.
            // Should look into handling when $url already
            // has query vars on it.
            $url .= '?' . http_build_query($this->config['query']);
            unset($this->config['query']);
        }

        $curlOptions[CURLOPT_URL] = $url;
        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $curlOptions[CURLOPT_HEADER] = true;
        $curlOptions[CURLOPT_FRESH_CONNECT] = true;
        // Disable @file uploads in post data.
        $curlOptions[CURLOPT_SAFE_UPLOAD] = true;

        $curlOptions = $this->setCURLOptions($curlOptions, $this->config);
        $curlOptions = $this->applyMethod($method, $curlOptions);
        $curlOptions = $this->applyRequestHeaders($curlOptions);

        // Do we need to delay this request?
        if ($this->config['delay'] > 0) {
            usleep((int)$this->config['delay'] * 1000000);
        }

        $output = $this->sendRequest($curlOptions);

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
            $this->response->setBody($body);
        } else {
            $this->response->setBody($output);
        }

        return $this->response;
    }

    /**
     * Convenience method for sending a GET request.
     * @param string $url
     * @param array $data
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function get(string $url, array $options = []): Response
    {
        return $this->request('get', $url, $options);
    }

    /**
     * Convenience method for sending a DELETE request.
     * @param string $url
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function delete(string $url, array $options = []): Response
    {
        return $this->request('delete', $url, $options);
    }

    /**
     * Convenience method for sending a HEAD request.
     * @param string $url
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function head(string $url, array $options = []): Response
    {
        return $this->request('head', $url, $options);
    }

    /**
     * Convenience method for sending an OPTIONS request.
     * @param string $url
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function options(string $url, array $options = []): Response
    {
        return $this->request('options', $url, $options);
    }

    /**
     * Convenience method for sending a PATCH request.
     * @param string $url
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function patch(string $url, array $options = []): Response
    {
        return $this->request('patch', $url, $options);
    }

    /**
     * Convenience method for sending a POST request.
     * @param string $url
     * @param array $data
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function post(string $url, array $options = []): Response
    {
        return $this->request('post', $url, $options);
    }

    /**
     * Convenience method for sending a PUT request.
     * @param string $url
     * @param array $options
     * @return Response
     * @throws HttpException
     */
    public function put(string $url, array $options = []): Response
    {
        return $this->request('put', $url, $options);
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
     * @param null $file
     * @return CurlRequest
     */
    public function file($file = null): self
    {
        $this->file = $file;

        return $this;
    }
}
