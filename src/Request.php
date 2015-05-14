<?php
namespace Wandu\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    use MessageTrait, RequestTrait;

    /**
     * @param UriInterface $uri
     * @param $method
     * @param StreamInterface $body
     * @param array $headers
     */
    public function __construct(
        UriInterface $uri = null,
        $method = null,
        StreamInterface $body = null,
        array $headers = []
    ) {
        $this->uri = $uri;
        $this->method = $this->filterMethod($method);
        $this->body = $body;
        $this->headers = $headers;
    }
}
