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
     * @param \Psr\Http\Message\StreamInterface|null $body
     */
    public function __construct($protocolVersion = '1.1', array $headers = [], StreamInterface $body = null)
    {
        $this->protocolVersion = $protocolVersion;
        foreach ($headers as $name => $header) {
            $lowerName = strtolower($name);
            $this->headerNames[$lowerName] = $name;
            $this->headers[$name] = $this->filterHeaderValue($header, $lowerName);
        }
        $this->body = $body;
    }
}
