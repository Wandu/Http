<?php
namespace Wandu\Http\Exception;

use Psr\Http\Message\ResponseInterface;

class ForbiddenException extends AbstractHttpException
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $attributes
     */
    public function __construct(
        ResponseInterface $response = null,
        array $attributes = []
    ) {
        parent::__construct(403, $response, $attributes);
    }
}
