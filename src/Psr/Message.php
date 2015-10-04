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
     * @param StreamInterface $body
     */
    public function __construct($protocolVersion = '1.1', array $headers = [], StreamInterface $body = null)
    {
        $this->protocolVersion = $protocolVersion;
        foreach ($headers as $name => $header) {
            $this->headerNames[strtolower($name)] = $name;
            $this->headers[$name] = $header;
        }
        $this->body = $body;
    }
}
