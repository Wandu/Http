<?php
namespace Wandu\Http\Exception;

use Exception;
use Psr\Http\Message\ResponseInterface;
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
     */
    public function __construct($statusCode = 500, $reasonPhrase = '')
    {
        $this->validStatusCode($statusCode);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $this->filterReasonPhrase($statusCode, $reasonPhrase);
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        throw new RuntimeException("cannot change status in HttpException.");
    }
}
