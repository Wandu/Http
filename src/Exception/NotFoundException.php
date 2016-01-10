<?php
namespace Wandu\Http\Exception;

class NotFoundException extends HttpException
{
    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     */
    public function __construct($statusCode = 404, $reasonPhrase = '')
    {
        parent::__construct($statusCode, $reasonPhrase);
    }
}
