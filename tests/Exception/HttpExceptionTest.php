<?php
namespace Wandu\Http\Exception;

use Mockery;
use PHPUnit_Framework_TestCase;

class HttpExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetDefaultValues()
    {
        $httpException = new HttpException();

        $this->assertSame(500, $httpException->getStatusCode());
        $this->assertSame("Internal Server Error", $httpException->getReasonPhrase());


        $httpException = new HttpException(500, "Hello World?");

        $this->assertSame(500, $httpException->getStatusCode());
        $this->assertSame("Hello World?", $httpException->getReasonPhrase());
    }
}
