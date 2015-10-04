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
     * @param string $reasonPhrase
     * @param string $protocolVersion
     * @param array $headers
     * @param \Psr\Http\Message\StreamInterface $body
     */
    public function __construct(
        $statusCode = 200,
        $reasonPhrase = '',
        $protocolVersion = '1.1',
        array $headers = [],
        StreamInterface $body = null
    ) {
        $this->validStatusCode($statusCode);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $this->filterReasonPhrase($statusCode, $reasonPhrase);

        parent::__construct($protocolVersion, $headers, $body);
    }
}
