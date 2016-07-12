<?php
namespace Wandu\Http\Exception;

use Psr\Http\Message\ResponseInterface;
use Wandu\Http\Psr\Response;

class BadRequestException extends HttpException
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $attributes
     */
    public function __construct(
        ResponseInterface $response = null,
        array $attributes = []
    ) {
        $response = $response ?: new Response();
        parent::__construct($response->withStatus(400), $attributes);
    }
}
