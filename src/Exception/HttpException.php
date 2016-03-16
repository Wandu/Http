<?php
namespace Wandu\Http\Exception;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Wandu\Http\Traits\MessageTrait;
use Wandu\Http\Traits\ResponseTrait;

class HttpException extends Exception implements ResponseInterface
{
    use MessageTrait;
    use ResponseTrait;

    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     * @param \Psr\Http\Message\StreamInterface|null $body
     * @param array $headers
     * @param string $protocolVersion
     */
    public function __construct(
        $statusCode = 500,
        $reasonPhrase = '',
        StreamInterface $body = null,
        array $headers = [],
        $protocolVersion = '1.1'
    ) {
        $this->validStatusCode($statusCode);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $this->filterReasonPhrase($statusCode, $reasonPhrase);

        $this->body = $body;
        foreach ($headers as $name => $header) {
            $this->headerNames[strtolower($name)] = $name;
            $this->headers[$name] = $header;
        }
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        throw new RuntimeException("cannot change status in HttpException.");
    }
}
