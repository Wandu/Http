<?php
namespace Wandu\Http\Factory;

use PHPUnit_Framework_TestCase;
use Mockery;
use Wandu\Http\UploadedFile;

class ServerRequestFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testGetUriDefault()
    {
        $server = array (
            'SERVER_NAME' => '0.0.0.0',
            'SERVER_PORT' => '8002',
            'REQUEST_URI' => '/abk?sdnkf',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => 'sdnkf',
            'HTTP_HOST' => 'localhost:8002',
        );
        $this->assertEquals('http://localhost:8002/abk?sdnkf', ServerRequestFactory::getUri($server)->__toString());
    }

    public function testGetUriMinimum()
    {
        $server = array (
            'SERVER_NAME' => '0.0.0.0',
            'SERVER_PORT' => '8002',
            'REQUEST_URI' => '/abk?sdnkf',
        );
        $this->assertEquals('http://0.0.0.0:8002/abk?sdnkf', ServerRequestFactory::getUri($server)->__toString());
    }
}
