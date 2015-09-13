<?php
namespace Wandu\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Wandu\Http\Traits\RequestTrait;

class Request extends Message implements RequestInterface
{
    use RequestTrait;

    /**
     * @param string $httpVersion
     * @param string $method
     * @param UriInterface $uri
     * @param array $headers
     * @param StreamInterface $body
     */
    public function __construct(
        $httpVersion,
        $method = null,
        UriInterface $uri = null,
        array $headers = [],
        StreamInterface $body = null
    ) {
        $this->method = $this->filterMethod($method);
        $this->uri = $uri;
        parent::__construct($httpVersion, $headers, $body);
    }
}
