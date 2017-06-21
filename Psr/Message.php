<?php
namespace Wandu\Http\Psr;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Wandu\Http\Traits\MessageTrait;

class Message implements MessageInterface
{
    use MessageTrait;

    /**
     * @param string $protocolVersion
     * @param array $headers
     * @param \Psr\Http\Message\StreamInterface $body
     */
    public function __construct(StreamInterface $body = null, array $headers = [], $protocolVersion = '1.1')
    {
        $this->body = $body;
        foreach ($headers as $name => $header) {
            $lowerName = strtolower($name);
            $this->headerNames[$lowerName] = $name;
            $this->headers[$name] = $this->filterHeaderValue($header);
        }
        $this->protocolVersion = $protocolVersion;
    }
}
