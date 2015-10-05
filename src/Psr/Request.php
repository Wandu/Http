<?php
namespace Wandu\Http\Psr;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Wandu\Http\Traits\RequestTrait;

class Request extends Message implements RequestInterface
{
    use RequestTrait;

    /**
     * @param string $method
     * @param \Psr\Http\Message\UriInterface $uri
     * @param string $protocolVersion
     * @param array $headers
     * @param \Psr\Http\Message\StreamInterface $body
     */
    public function __construct(
        $method = null,
        UriInterface $uri = null,
        $protocolVersion = '1.1',
        array $headers = [],
        StreamInterface $body = null
    ) {
        $this->method = $this->filterMethod($method);
        $this->uri = $uri;
        parent::__construct($protocolVersion, $headers, $body);
    }
}
