<?php
namespace Wandu\Http\Exception;

class MethodNotAllowedException extends HttpException
{
    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     */
    public function __construct($statusCode = 405, $reasonPhrase = '')
    {
        parent::__construct($statusCode, $reasonPhrase);
    }
}
