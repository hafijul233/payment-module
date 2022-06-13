<?php

namespace HishabKitab\Payment\Http;

use DateTime;
use DateTimeZone;
use HishabKitab\Payment\Exceptions\HttpException;

class Response
{
    /**
     * HTTP status codes
     *
     * @var array
     */
    protected static $statusCodes = [
        // 1xx: Informational
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // http://www.iana.org/go/rfc2518
        103 => 'Early Hints', // http://www.ietf.org/rfc/rfc8297.txt
        // 2xx: Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information', // 1.1
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // http://www.iana.org/go/rfc4918
        208 => 'Already Reported', // http://www.iana.org/go/rfc5842
        226 => 'IM Used', // 1.1; http://www.ietf.org/rfc/rfc3229.txt
        // 3xx: Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // Formerly 'Moved Temporarily'
        303 => 'See Other', // 1.1
        304 => 'Not Modified',
        305 => 'Use Proxy', // 1.1
        306 => 'Switch Proxy', // No longer used
        307 => 'Temporary Redirect', // 1.1
        308 => 'Permanent Redirect', // 1.1; Experimental; http://www.ietf.org/rfc/rfc7238.txt
        // 4xx: Client error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large', // https://www.iana.org/assignments/http-status-codes/http-status-codes.xml
        414 => 'URI Too Long', // https://www.iana.org/assignments/http-status-codes/http-status-codes.xml
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => "I'm a teapot", // April's Fools joke; http://www.ietf.org/rfc/rfc2324.txt
        // 419 (Authentication Timeout) is a non-standard status code with unknown origin
        421 => 'Misdirected Request', // http://www.iana.org/go/rfc7540 Section 9.1.2
        422 => 'Unprocessable Content', // https://www.iana.org/assignments/http-status-codes/http-status-codes.xml
        423 => 'Locked', // http://www.iana.org/go/rfc4918
        424 => 'Failed Dependency', // http://www.iana.org/go/rfc4918
        425 => 'Too Early', // https://datatracker.ietf.org/doc/draft-ietf-httpbis-replay/
        426 => 'Upgrade Required',
        428 => 'Precondition Required', // 1.1; http://www.ietf.org/rfc/rfc6585.txt
        429 => 'Too Many Requests', // 1.1; http://www.ietf.org/rfc/rfc6585.txt
        431 => 'Request Header Fields Too Large', // 1.1; http://www.ietf.org/rfc/rfc6585.txt
        451 => 'Unavailable For Legal Reasons', // http://tools.ietf.org/html/rfc7725
        499 => 'Client Closed Request', // http://lxr.nginx.org/source/src/http/ngx_http_request.h#0133
        // 5xx: Server error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates', // 1.1; http://www.ietf.org/rfc/rfc2295.txt
        507 => 'Insufficient Storage', // http://www.iana.org/go/rfc4918
        508 => 'Loop Detected', // http://www.iana.org/go/rfc5842
        510 => 'Not Extended', // http://www.ietf.org/rfc/rfc2774.txt
        511 => 'Network Authentication Required', // http://www.ietf.org/rfc/rfc6585.txt
        599 => 'Network Connect Timeout Error', // https://httpstatuses.com/599
    ];
    /**
     * List of all HTTP request headers.
     *
     * @var array<string, Header>
     */
    protected $headers = [];

    //--------------------------------------------------------------------
    // Body
    //--------------------------------------------------------------------
    /**
     * Holds a map of lower-case header names
     * and their normal-case key as it is in $headers.
     * Used for case-insensitive header access.
     *
     * @var array
     */
    protected $headerMap = [];
    /**
     * Type of format the body is in.
     * Valid: html, json, xml
     *
     * @var string
     */
    protected $bodyFormat = 'html';

    //--------------------------------------------------------------------
    // Headers
    //--------------------------------------------------------------------
    /**
     * The current reason phrase for this response.
     * If empty string, will use the default provided for the status code.
     *
     * @var string
     */
    protected $reason = '';
    /**
     * The current status code for this response.
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @var int
     */
    protected $statusCode = 200;
    /**
     * If true, will not write output. Useful during testing.
     *
     * @var bool
     *
     * @internal Used for framework testing, should not be relied on otherwise
     */
    protected $pretend = false;
    /**
     * Protocol version
     *
     * @var string
     */
    protected $protocolVersion;
    /**
     * List of valid protocol versions
     *
     * @var array
     */
    protected $validProtocolVersions = [
        '1.0',
        '1.1',
        '2.0',
    ];
    /**
     * Message body
     *
     * @var mixed
     */
    protected $body;

    /**
     * Response constructor.
     */
    public function __construct()
    {
        // Default to a non-caching page.
        // Also ensures that a Cache-control header exists.
        $this->noCache();

        // Default to an HTML Content-Type. Devs can override if needed.
        $this->setContentType('text/html');
    }

    /**
     * Sets the appropriate headers to ensure this response
     * is not cached by the browsers.
     *
     * @return Response
     *
     * @todo Recommend researching these directives, might need: 'private', 'no-transform', 'no-store', 'must-revalidate'
     *
     * @see DownloadResponse::noCache()
     */
    public function noCache()
    {
        $this->removeHeader('Cache-control');
        $this->setHeader('Cache-control', ['no-store', 'max-age=0', 'no-cache']);

        return $this;
    }

    /**
     * Appends data to the body of the current message.
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function appendBody($data): self
    {
        $this->body .= (string)$data;

        return $this;
    }

    /**
     * Adds an additional header value to any headers that accept
     * multiple values (i.e. are an array or implement ArrayAccess)
     *
     * @return $this
     */
    public function prependHeader(string $name, string $value): self
    {
        $origName = $this->getHeaderName($name);

        $this->headers[$origName]->prependValue($value);

        return $this;
    }

    /**
     * Converts the $body into JSON and sets the Content Type header.
     *
     * @param array|string $body
     *
     * @return $this
     */
    public function setJSON($body, bool $unencoded = false)
    {
        $this->body = $this->formatBody($body, 'json' . ($unencoded ? '-unencoded' : ''));

        return $this;
    }

    //--------------------------------------------------------------------
    // Convenience Methods
    //--------------------------------------------------------------------

    /**
     * Handles conversion of the of the data into the appropriate format,
     * and sets the correct Content-Type header for our response.
     *
     * @param array|string $body
     * @param string $format Valid: json, xml
     *
     * @return mixed
     * @throws InvalidArgumentException If the body property is not string or array.
     *
     */
    protected function formatBody($body, string $format)
    {
        $this->bodyFormat = ($format === 'json-unencoded' ? 'json' : $format);
        $mime = "application/{$this->bodyFormat}";
        $this->setContentType($mime);

        // Nothing much to do for a string...
        if (! is_string($body) || $format === 'json-unencoded') {
            $body = Services::format()->getFormatter($mime)->format($body);
        }

        return $body;
    }

    /**
     * Set the Link Header
     *
     * @see http://tools.ietf.org/html/rfc5988
     *
     * @param PagerInterface $pager
     * @return self
     *
     * @todo Recommend moving to Pager
     *
     * public function setLink(PagerInterface $pager): self
     * {
     * $links = '';
     *
     * if ($previous = $pager->getPreviousPageURI()) {
     * $links .= '<' . $pager->getPageURI($pager->getFirstPage()) . '>; rel="first",';
     * $links .= '<' . $previous . '>; rel="prev"';
     * }
     *
     * if (($next = $pager->getNextPageURI()) && $previous) {
     * $links .= ',';
     * }
     *
     * if ($next) {
     * $links .= '<' . $next . '>; rel="next",';
     * $links .= '<' . $pager->getPageURI($pager->getLastPage()) . '>; rel="last"';
     * }
     *
     * $this->setHeader('Link', $links);
     *
     * return $this;
     * }
     */

    /**
     * Sets the Content Type header for this response with the mime type
     * and, optionally, the charset.
     *
     * @return Response
     */
    public function setContentType(string $mime, string $charset = 'UTF-8'): self
    {
        // add charset attribute if not already there and provided as parm
        if ((strpos($mime, 'charset=') < 1) && ! empty($charset)) {
            $mime .= '; charset=' . $charset;
        }

        $this->removeHeader('Content-Type'); // replace existing content type
        $this->setHeader('Content-Type', $mime);

        return $this;
    }

    /**
     * Removes a header from the list of headers we track.
     *
     * @return $this
     */
    public function removeHeader(string $name): self
    {
        $origName = $this->getHeaderName($name);
        unset($this->headers[$origName], $this->headerMap[strtolower($name)]);

        return $this;
    }

    /**
     * Returns the current body, converted to JSON is it isn't already.
     *
     * @return mixed|string
     * @throws InvalidArgumentException If the body property is not array.
     *
     */
    public function getJSON()
    {
        $body = $this->body;

        if ($this->bodyFormat !== 'json') {
            $body = Services::format()->getFormatter('application/json')->format($body);
        }

        return $body ?: null;
    }

    /**
     * Converts $body into XML, and sets the correct Content-Type.
     *
     * @param array|string $body
     *
     * @return $this
     */
    public function setXML($body)
    {
        $this->body = $this->formatBody($body, 'xml');

        return $this;
    }

    /**
     * Retrieves the current body into XML and returns it.
     *
     * @return mixed|string
     * @throws InvalidArgumentException If the body property is not array.
     *
     */
    public function getXML()
    {
        $body = $this->body;

        if ($this->bodyFormat !== 'xml') {
            $body = Services::format()->getFormatter('application/xml')->format($body);
        }

        return $body;
    }

    /**
     * A shortcut method that allows the developer to set all of the
     * cache-control headers in one method call.
     *
     * The options array is used to provide the cache-control directives
     * for the header. It might look something like:
     *
     *      $options = [
     *          'max-age'  => 300,
     *          's-maxage' => 900
     *          'etag'     => 'abcde',
     *      ];
     *
     * Typical options are:
     *  - etag
     *  - last-modified
     *  - max-age
     *  - s-maxage
     *  - private
     *  - public
     *  - must-revalidate
     *  - proxy-revalidate
     *  - no-transform
     *
     * @return Response
     */
    public function setCache(array $options = [])
    {
        if (empty($options)) {
            return $this;
        }

        $this->removeHeader('Cache-Control');
        $this->removeHeader('ETag');

        // ETag
        if (isset($options['etag'])) {
            $this->setHeader('ETag', $options['etag']);
            unset($options['etag']);
        }

        // Last Modified
        if (isset($options['last-modified'])) {
            $this->setLastModified($options['last-modified']);

            unset($options['last-modified']);
        }

        $this->setHeader('Cache-control', $options);

        return $this;
    }

    //--------------------------------------------------------------------
    // Cache Control Methods
    //
    // http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
    //--------------------------------------------------------------------

    /**
     * Sets the Last-Modified date header.
     *
     * $date can be either a string representation of the date or,
     * preferably, an instance of DateTime.
     *
     * @param DateTime|string $date
     *
     * @return Response
     */
    public function setLastModified($date)
    {
        if ($date instanceof DateTime) {
            $date->setTimezone(new DateTimeZone('UTC'));
            $this->setHeader('Last-Modified', $date->format('D, d M Y H:i:s') . ' GMT');
        } elseif (is_string($date)) {
            $this->setHeader('Last-Modified', $date);
        }

        return $this;
    }

    /**
     * Sends the output to the browser.
     *
     * @return Response
     */
    public function send()
    {
        // If we're enforcing a Content Security Policy,
        // we need to give it a chance to build out it's headers.
        if ($this->CSP->enabled()) {
            $this->CSP->finalize($this);
        } else {
            $this->body = str_replace(['{csp-style-nonce}', '{csp-script-nonce}'], '', $this->body ?? '');
        }

        $this->sendHeaders();
        $this->sendCookies();
        $this->sendBody();

        return $this;
    }

    /**
     * Sends the headers of this HTTP response to the browser.
     *
     * @return Response
     */
    public function sendHeaders()
    {
        // Have the headers already been sent?
        if ($this->pretend || headers_sent()) {
            return $this;
        }

        // Per spec, MUST be sent with each request, if possible.
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html
        if (! isset($this->headers['Date']) && PHP_SAPI !== 'cli-server') {
            $this->setDate(DateTime::createFromFormat('U', (string)time()));
        }

        // HTTP Status
        header(sprintf('HTTP/%s %s %s', $this->getProtocolVersion(), $this->getStatusCode(), $this->getReasonPhrase()), true, $this->getStatusCode());

        // Send all of our headers
        foreach (array_keys($this->getHeaders()) as $name) {
            header($name . ': ' . $this->getHeaderLine($name), false, $this->getStatusCode());
        }

        return $this;
    }

    //--------------------------------------------------------------------
    // Output Methods
    //--------------------------------------------------------------------

    /**
     * Sets the date header
     *
     * @param DateTime $date
     * @return self
     */
    public function setDate(DateTime $date): self
    {
        $date->setTimezone(new DateTimeZone('UTC'));

        $this->setHeader('Date', $date->format('D, d M Y H:i:s') . ' GMT');

        return $this;
    }

    /**
     * Returns the HTTP Protocol Version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion ?? '1.1';
    }

    /**
     * Sets the HTTP protocol version.
     *
     * @return $this
     * @throws HttpException For invalid protocols
     *
     */
    public function setProtocolVersion(string $version): self
    {
        if (! is_numeric($version)) {
            $version = substr($version, strpos($version, '/') + 1);
        }

        // Make sure that version is in the correct format
        $version = number_format((float)$version, 1);

        /*if (! in_array($version, $this->validProtocolVersions, true)) {
            throw HttpException::forInvalidHTTPProtocol(implode(', ', $this->validProtocolVersions));
        }*/

        $this->protocolVersion = $version;

        return $this;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the getServer's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     * @throws HttpException
     */
    public function getStatusCode(): int
    {
        if (empty($this->statusCode)) {
            throw HttpException::forMissingResponseStatus();
        }

        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, will default recommended reason phrase for
     * the response's status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param int $code The 3-digit integer result code to set.
     * @param string $reason The reason phrase to use with the
     *                       provided status code; if none is provided, will
     *                       default to the IANA name.
     *
     * @throws HttpException For invalid status code arguments.
     *
     * @return $this
     */
    public function setStatusCode(int $code, string $reason = '')
    {
        // Valid range?
        if ($code < 100 || $code > 599) {
            throw HttpException::forInvalidStatusCode($code);
        }

        // Unknown and no message?
        if (! array_key_exists($code, static::$statusCodes) && empty($reason)) {
            throw HttpException::forUnkownStatusCode($code);
        }

        $this->statusCode = $code;

        $this->reason = ! empty($reason) ? $reason : static::$statusCodes[$code];

        return $this;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        if ($this->reason === '') {
            return ! empty($this->statusCode) ? static::$statusCodes[$this->statusCode] : '';
        }

        return $this->reason;
    }

    /**
     * Returns an array containing all headers.
     *
     * @return array<string, Header> An array of the request headers
     *
     * @deprecated Use Message::headers() to make room for PSR-7
     *
     * @codeCoverageIgnore
     */
    public function getHeaders(): array
    {
        return $this->headers();
    }

    /**
     * Returns an array containing all Headers.
     *
     * @return array<string, Header> An array of the Header objects
     */
    public function headers(): array
    {
        // If no headers are defined, but the user is
        // requesting it, then it's likely they want
        // it to be populated so do that...
        if (empty($this->headers)) {
            $this->populateHeaders();
        }

        return $this->headers;
    }

    /**
     * Populates the $headers array with any headers the getServer knows about.
     */
    public function populateHeaders(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? getenv('CONTENT_TYPE');
        if (! empty($contentType)) {
            $this->setHeader('Content-Type', $contentType);
        }
        unset($contentType);

        foreach (array_keys($_SERVER) as $key) {
            if (sscanf($key, 'HTTP_%s', $header) === 1) {
                // take SOME_HEADER and turn it into Some-Header
                $header = str_replace('_', ' ', strtolower($header));
                $header = str_replace(' ', '-', ucwords($header));

                $this->setHeader($header, $_SERVER[$key]);

                // Add us to the header map so we can find them case-insensitively
                $this->headerMap[strtolower($header)] = $header;
            }
        }
    }

    /**
     * Sets a header and it's value.
     *
     * @param array|string|null $value
     *
     * @return $this
     */
    public function setHeader(string $name, $value): self
    {
        $origName = $this->getHeaderName($name);

        if (isset($this->headers[$origName]) && is_array($this->headers[$origName]->getValue())) {
            if (! is_array($value)) {
                $value = [$value];
            }

            foreach ($value as $v) {
                $this->appendHeader($origName, $v);
            }
        } else {
            $this->headers[$origName] = new Header($origName, $value);
            $this->headerMap[strtolower($origName)] = $origName;
        }

        return $this;
    }

    /**
     * Takes a header name in any case, and returns the
     * normal-case version of the header.
     */
    protected function getHeaderName(string $name): string
    {
        return $this->headerMap[strtolower($name)] ?? $name;
    }

    /**
     * Adds an additional header value to any headers that accept
     * multiple values (i.e. are an array or implement ArrayAccess)
     *
     * @return $this
     */
    public function appendHeader(string $name, ?string $value): self
    {
        $origName = $this->getHeaderName($name);

        array_key_exists($origName, $this->headers)
            ? $this->headers[$origName]->appendValue($value)
            : $this->setHeader($name, $value);

        return $this;
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     */
    public function getHeaderLine(string $name): string
    {
        $origName = $this->getHeaderName($name);

        if (! array_key_exists($origName, $this->headers)) {
            return '';
        }

        return $this->headers[$origName]->getValueLine();
    }

    /**
     * Actually sets the cookies.
     */
    protected function sendCookies()
    {
        if ($this->pretend) {
            return;
        }

        $this->dispatchCookies();
    }

    private function dispatchCookies(): void
    {
        /** @var IncomingRequest $request */
        $request = Services::request();

        foreach ($this->cookieStore->display() as $cookie) {
            if ($cookie->isSecure() && ! $request->isSecure()) {
                throw SecurityException::forDisallowedAction();
            }

            $name = $cookie->getPrefixedName();
            $value = $cookie->getValue();
            $options = $cookie->getOptions();

            if ($cookie->isRaw()) {
                $this->doSetRawCookie($name, $value, $options);
            } else {
                $this->doSetCookie($name, $value, $options);
            }
        }

        $this->cookieStore->clear();
    }

    /**
     * Extracted call to `setrawcookie()` in order to run unit tests on it.
     *
     * @codeCoverageIgnore
     */
    private function doSetRawCookie(string $name, string $value, array $options): void
    {
        setrawcookie($name, $value, $options);
    }

    /**
     * Extracted call to `setcookie()` in order to run unit tests on it.
     *
     * @codeCoverageIgnore
     */
    private function doSetCookie(string $name, string $value, array $options): void
    {
        setcookie($name, $value, $options);
    }

    /**
     * Sends the Body of the message to the browser.
     *
     * @return Response
     */
    public function sendBody()
    {
        echo $this->body;

        return $this;
    }

    /**
     * Perform a redirect to a new URL, in two flavors: header or location.
     *
     * @param string $uri The URI to redirect to
     * @param int $code The type of redirection, defaults to 302
     *
     * @return $this
     * @throws HttpException For invalid status code.
     *
     */
    public function redirect(string $uri, string $method = 'auto', ?int $code = null)
    {
        // Assume 302 status code response; override if needed
        if (empty($code)) {
            $code = 302;
        }

        // IIS environment likely? Use 'refresh' for better compatibility
        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) {
            $method = 'refresh';
        }

        // override status code for HTTP/1.1 & higher
        // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
        if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $this->getProtocolVersion() >= 1.1 && $method !== 'refresh') {
            $code = ($_SERVER['REQUEST_METHOD'] !== 'GET') ? 303 : ($code === 302 ? 307 : $code);
        }

        switch ($method) {
            case 'refresh':
                $this->setHeader('Refresh', '0;url=' . $uri);

                break;

            default:
                $this->setHeader('Location', $uri);

                break;
        }

        $this->setStatusCode($code);

        return $this;
    }

    /**
     * Returns the `CookieStore` instance.
     *
     * @return CookieStore
     */
    public function getCookieStore()
    {
        return $this->cookieStore;
    }

    /**
     * Checks to see if the Response has a specified cookie or not.
     */
    public function hasCookie(string $name, ?string $value = null, string $prefix = ''): bool
    {
        $prefix = $prefix ?: Cookie::setDefaults()['prefix']; // to retain BC

        return $this->cookieStore->has($name, $prefix, $value);
    }

    /**
     * Returns the cookie
     *
     * @return Cookie|Cookie[]|null
     */
    public function getCookie(?string $name = null, string $prefix = '')
    {
        if ((string)$name === '') {
            return $this->cookieStore->display();
        }

        try {
            $prefix = $prefix ?: Cookie::setDefaults()['prefix']; // to retain BC

            return $this->cookieStore->get($name, $prefix);
        } catch (CookieException $e) {
            log_message('error', $e->getMessage());

            return null;
        }
    }

    /**
     * Sets a cookie to be deleted when the response is sent.
     *
     * @return $this
     */
    public function deleteCookie(string $name = '', string $domain = '', string $path = '/', string $prefix = '')
    {
        if ($name === '') {
            return $this;
        }

        $prefix = $prefix ?: Cookie::setDefaults()['prefix']; // to retain BC

        $prefixed = $prefix . $name;
        $store = $this->cookieStore;
        $found = false;

        foreach ($store as $cookie) {
            if ($cookie->getPrefixedName() === $prefixed) {
                if ($domain !== $cookie->getDomain()) {
                    continue;
                }

                if ($path !== $cookie->getPath()) {
                    continue;
                }

                $cookie = $cookie->withValue('')->withExpired();
                $found = true;

                $this->cookieStore = $store->put($cookie);

                break;
            }
        }

        if (! $found) {
            $this->setCookie($name, '', '', $domain, $path, $prefix);
        }

        return $this;
    }

    /**
     * Set a cookie
     *
     * Accepts an arbitrary number of binds (up to 7) or an associative
     * array in the first parameter containing all the values.
     *
     * @param array|Cookie|string $name Cookie name / array containing binds / Cookie object
     * @param string $value Cookie value
     * @param string $expire Cookie expiration time in seconds
     * @param string $domain Cookie domain (e.g.: '.yourdomain.com')
     * @param string $path Cookie path (default: '/')
     * @param string $prefix Cookie name prefix
     * @param bool $secure Whether to only transfer cookies via SSL
     * @param bool $httponly Whether only make the cookie accessible via HTTP (no javascript)
     * @param string|null $samesite
     *
     * @return $this
     */
    public function setCookie(
        $name,
        $value = '',
        $expire = '',
        $domain = '',
        $path = '/',
        $prefix = '',
        $secure = false,
        $httponly = false,
        $samesite = null
    ) {
        if ($name instanceof Cookie) {
            $this->cookieStore = $this->cookieStore->put($name);

            return $this;
        }

        if (is_array($name)) {
            // always leave 'name' in last place, as the loop will break otherwise, due to $$item
            foreach (['samesite', 'value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name'] as $item) {
                if (isset($name[$item])) {
                    ${$item} = $name[$item];
                }
            }
        }

        if (is_numeric($expire)) {
            $expire = $expire > 0 ? time() + $expire : 0;
        }

        $cookie = new Cookie($name, $value, [
            'expires' => $expire ?: 0,
            'domain' => $domain,
            'path' => $path,
            'prefix' => $prefix,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite ?? '',
        ]);

        $this->cookieStore = $this->cookieStore->put($cookie);

        return $this;
    }

    /**
     * Returns all cookies currently set.
     *
     * @return Cookie[]
     */
    public function getCookies()
    {
        return $this->cookieStore->display();
    }

    /**
     * Force a download.
     *
     * Generates the headers that force a download to happen. And
     * sends the file to the browser.
     *
     * @param string $filename The path to the file to send
     * @param string|null $data The data to be downloaded
     * @param bool $setMime Whether to try and send the actual MIME type
     *
     * @return DownloadResponse|null
     */
    public function download(string $filename = '', $data = '', bool $setMime = false)
    {
        if ($filename === '' || $data === '') {
            return null;
        }

        $filepath = '';
        if ($data === null) {
            $filepath = $filename;
            $filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
            $filename = end($filename);
        }

        $response = new DownloadResponse($filename, $setMime);

        if ($filepath !== '') {
            $response->setFilePath($filepath);
        } elseif ($data !== null) {
            $response->setBinary($data);
        }

        return $response;
    }

    /**
     * Returns the Message's body.
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the body of the current message.
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function setBody($data): self
    {
        $this->body = $data;

        return $this;
    }

    /**
     * Returns a single header object. If multiple headers with the same
     * name exist, then will return an array of header objects.
     *
     * @return array|Header|null
     *
     * @deprecated Use Message::header() to make room for PSR-7
     *
     * @codeCoverageIgnore
     */
    public function getHeader(string $name)
    {
        return $this->header($name);
    }

    /**
     * Returns a single Header object. If multiple headers with the same
     * name exist, then will return an array of header objects.
     *
     * @param string $name
     *
     * @return Header|null
     */
    public function header(string $name): ?Header
    {
        $origName = $this->getHeaderName($name);

        return $this->headers[$origName] ?? null;
    }

    /**
     * Determines whether a header exists.
     */
    public function hasHeader(string $name): bool
    {
        $origName = $this->getHeaderName($name);

        return isset($this->headers[$origName]);
    }

    /**
     * Turns "pretend" mode on or off to aid in testing.
     * Note that this is not a part of the interface so
     * should not be relied on outside of internal testing.
     *
     * @return $this
     */
    public function pretend(bool $pretend = true)
    {
        $this->pretend = $pretend;

        return $this;
    }

    /**
     * Gets the response response phrase associated with the status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @deprecated Use getReasonPhrase()
     *
     * @codeCoverageIgnore
     */
    public function getReason(): string
    {
        return $this->getReasonPhrase();
    }
}
