<?php
namespace Wandu\Http\Exception;

class UnauthorizedException extends HttpException
{
    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     * @param \Psr\Http\Message\StreamInterface $body
     * @param array $headers
     * @param string $protocolVersion
     * @param array $attributes
     */
    public function __construct(
        $statusCode = 401,
        $reasonPhrase = '',
        $body = null,
        array $headers = [],
        $protocolVersion = '1.1',
        array $attributes = []
    ) {
        parent::__construct($statusCode, $reasonPhrase, $body, $headers, $protocolVersion, $attributes);
    }
}
