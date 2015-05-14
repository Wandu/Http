<?php
namespace Wandu\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{
    use MessageTrait;

    public function __construct($protocolVersion, array $headers = [], StreamInterface $body = null)
    {
        $this->protocolVersion = $protocolVersion;
        $this->headers = $headers;
        foreach ($headers as $name => $header) {
            $this->headerNames[strtolower($name)] = $name;
        }
        $this->body = $body;
    }
}
