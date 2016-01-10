<?php
namespace Wandu\Http\Exception;

class ForbiddenException extends HttpException
{
    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     */
    public function __construct($statusCode = 403, $reasonPhrase = '')
    {
        parent::__construct($statusCode, $reasonPhrase);
    }
}
