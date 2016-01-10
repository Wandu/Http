<?php
namespace Wandu\Http\Exception;

class BadRequestException extends HttpException
{
    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     */
    public function __construct($statusCode = 400, $reasonPhrase = '')
    {
        parent::__construct($statusCode, $reasonPhrase);
    }
}
