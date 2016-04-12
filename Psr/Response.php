<?php
namespace Wandu\Http\Psr;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Wandu\Http\Traits\ResponseTrait;

class Response extends Message implements ResponseInterface
{
    use ResponseTrait;

    /**
     * @param int $statusCode
     * @param \Psr\Http\Message\StreamInterface $body
     * @param array $headers
     * @param string $reasonPhrase
     * @param string $protocolVersion
     */
    public function __construct(
        $statusCode = 200,
        StreamInterface $body = null,
        array $headers = [],
        $reasonPhrase = '',
        $protocolVersion = '1.1'
    ) {
        $this->validStatusCode($statusCode);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $this->filterReasonPhrase($statusCode, $reasonPhrase);

        parent::__construct($body, $headers, $protocolVersion);
    }
}
