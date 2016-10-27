<?php
namespace Wandu\Http\Exception;

class MethodNotAllowedException extends HttpException
{
    /**
     * @param \Psr\Http\Message\ResponseInterface|\Psr\Http\Message\StreamInterface|string $response
     * @param array $attributes
     */
    public function __construct($response = null, array $attributes = []) {
        parent::__construct($response, $attributes);
        $this->response = $this->response->withStatus(405);
    }
}
