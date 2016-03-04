<?php
namespace Wandu\Http\Psr\Factory;

use Psr\Http\Message\StreamInterface;
use Wandu\Http\Psr\Factory\Exception\CannotCreateRequestException;
use Wandu\Http\Psr\Request;
use Wandu\Http\Psr\Stream;
use Wandu\Http\Psr\Uri;

class RequestFactory
{
    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function createFromGlobals(array $serverParams = null)
    {
        if (!isset($serverParams)) {
            $serverParams = $_SERVER;
        }

        $method = isset($serverParams['REQUEST_METHOD']) ? $serverParams['REQUEST_METHOD'] : 'GET';
        $uri = isset($serverParams['REQUEST_URI']) ? $serverParams['REQUEST_URI'] : '/';

        $headers = $this->getPsrHeadersFromServerParams($serverParams);
        return new Request(
            $method,
            $this->makeUriObjectWithPsrHeaders($uri, $headers),
            '1.1',
            $headers,
            isset($stream) ? $stream : new Stream('php://memory')
        );
    }

    /**
     * @param array $plainHeaders
     * @param \Psr\Http\Message\StreamInterface|null $stream
     * @return \Psr\Http\Message\RequestInterface
     * @throws \Wandu\Http\Psr\Factory\Exception\CannotCreateRequestException
     */
    public function createRequest(array $plainHeaders, StreamInterface $stream = null)
    {
        if (count($plainHeaders) === 0) {
            throw new CannotCreateRequestException(
                'first parameter is array, and it must have at least one value.',
                100
            );
        }

        $httpInformation = explode(' ', array_shift($plainHeaders));
        if (count($httpInformation) !== 3) {
            throw new CannotCreateRequestException('wrong http header.', 101);
        }
        list ($method, $uri, $serverProtocol) = $httpInformation;

        $headers = $this->getPsrHeadersFromPlainHeader($plainHeaders);
        return new Request(
            $method,
            $this->makeUriObjectWithPsrHeaders($uri, $headers),
            explode('/', $serverProtocol)[1],
            $headers,
            isset($stream) ? $stream : new Stream('php://memory')
        );
    }

    /**
     * @param array $plainHeaders
     * @return array
     */
    protected function getPsrHeadersFromPlainHeader(array $plainHeaders)
    {
        $headers = [];
        foreach ($plainHeaders as $plainHeader) {
            list($key, $value) = array_map('trim', explode(':', $plainHeader, 2));
            $headers[strtolower($key)] = [$value];
        }
        return $headers;
    }

    /**
     * @param array $serverParams
     * @return array
     */
    protected function getPsrHeadersFromServerParams(array $serverParams)
    {
        $headers = array();
        foreach ($serverParams as $key => $value) {
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(substr($key, 5), '_', ' ');
                $name = strtr(ucwords(strtolower($name)), ' ', '-');
                $name = strtolower($name);
                $headers[$name] = explode(',', $value);
                continue;
            }
            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = substr($key, 8); // Content-
                $name = 'Content-' . (($name == 'MD5') ? $name : ucfirst(strtolower($name)));
                $name = strtolower($name);
                $headers[$name] = explode(',', $value);
                continue;
            }
        }
        return $headers;
    }

    /**
     * @param string $uri
     * @param array $headers
     * @return \Psr\Http\Message\UriInterface
     */
    protected function makeUriObjectWithPsrHeaders($uri = '/', array $headers = [])
    {
        $plainUri = isset($headers['host'][0]) ? 'http://' . $headers['host'][0] : '';
        if ($uri !== '/' || ($uri === '/' && $plainUri === '')) {
            $plainUri .= $uri;
        }
        return new Uri($plainUri);
    }
}
