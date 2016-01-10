<?php
namespace Wandu\Http\Exception;

class UnauthorizedException extends HttpException
{
    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     */
    public function __construct($statusCode = 401, $reasonPhrase = '')
    {
        parent::__construct($statusCode, $reasonPhrase);
    }
}
