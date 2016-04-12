<?php
namespace Wandu\Http\Psr;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Wandu\Http\Traits\RequestTrait;

class Request extends Message implements RequestInterface
{
    use RequestTrait;

    /**
     * @param string $method
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param \Psr\Http\Message\StreamInterface $body
     * @param array $headers
     * @param string $protocolVersion
     */
    public function __construct(
        $method = null,
        $uri = null,
        StreamInterface $body = null,
        array $headers = [],
        $protocolVersion = '1.1'
    ) {
        $this->method = $this->filterMethod($method);
        $this->uri = is_string($uri) ? new Uri($uri) : $uri;
        parent::__construct($body, $headers, $protocolVersion);
    }
}
