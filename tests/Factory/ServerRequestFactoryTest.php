<?php
namespace Wandu\Http\Factory;

use Mockery;
use PHPUnit_Framework_TestCase;

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


//    public function testConstructWithApplicationJson()
//    {
//        $body = Mockery::mock(StreamInterface::class);
//        $body->shouldReceive('__toString')->andReturn('{"hello":[1,2,3,4,5]}');
//        $request = new ServerRequest(
//            [
//                'HTTP_CONTENT_TYPE' => 'application/json',
//            ],
//            [], [], [], [], [], 'GET', null, '1.1', [], $body
//        );
//
//        $this->assertEquals(['hello' => [1,2,3,4,5]], $request->getParsedBody());
//
//
//        $body = Mockery::mock(StreamInterface::class);
//        $body->shouldReceive('__toString')->andReturn('{"hello":"world"}');
//        $request = new ServerRequest(
//            [
//                'HTTP_CONTENT_TYPE' => 'application/json;charset=UTF-8',
//            ],
//            [], [], [], [], [], '1.1', null, $body
//        );
//
//        $this->assertEquals(['hello' => 'world'], $request->getParsedBody());
//    }


}
