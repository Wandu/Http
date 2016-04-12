<?php
namespace Wandu\Http\Psr\Factory;

use Psr\Http\Message\StreamInterface;
use Wandu\Http\Psr\Factory\Exception\CannotCreateRequestException;
use Wandu\Http\Psr\Request;
use Wandu\Http\Psr\Stream;

class RequestFactory
{
    use HelperTrait;

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
            isset($stream) ? $stream : new Stream('php://memory'),
            $headers,
            '1.1'
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
            isset($stream) ? $stream : new Stream('php://memory'),
            $headers,
            explode('/', $serverProtocol)[1]
        );
    }
}
