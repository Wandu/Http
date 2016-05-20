<?php
namespace Wandu\Http\Exception;

use Psr\Http\Message\ResponseInterface;

class InternalServerErrorException extends AbstractHttpException
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $attributes
     */
    public function __construct(
        ResponseInterface $response = null,
        array $attributes = []
    ) {
        parent::__construct(500, $response, $attributes);
    }
}
