<?php
namespace Wandu\Http\Exception;

class InternalServerErrorException extends HttpException
{
    /**
     * @param mixed $response
     * @param array $attributes
     */
    public function __construct($response = null, array $attributes = []) {
        parent::__construct($response, $attributes);
        $this->response = $this->response->withStatus(500);
    }
}
