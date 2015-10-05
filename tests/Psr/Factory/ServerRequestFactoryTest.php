<?php
namespace Wandu\Http\Psr\Factory;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\StreamInterface;

class ServerRequestFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Psr\Factory\ServerRequestFactory */
    protected $factory;

    public function setUp()
    {
        $mockFileFactory = Mockery::mock(UploadedFileFactory::class);
        $mockFileFactory->shouldReceive('fromFiles')->andReturn([]);

        $this->factory = new ServerRequestFactory($mockFileFactory);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetUriFromHeader()
    {
        $serverRequest = $this->factory->factory([
            'HTTP_HOST' => 'localhost:8002',
            'REQUEST_URI' => '/abk?sdnkf',
        ], [], [], [], []);

        $this->assertEquals('http://localhost:8002/abk?sdnkf', $serverRequest->getUri()->__toString());
    }

    public function testGetUriFromServerVariable()
    {
        $serverRequest = $this->factory->factory([
            'SERVER_NAME' => '0.0.0.0',
            'SERVER_PORT' => '8002',
            'REQUEST_URI' => '/abk?sdnkf',
        ], [], [], [], []);

        $this->assertEquals('http://0.0.0.0:8002/abk?sdnkf', $serverRequest->getUri()->__toString());
    }

    public function testGetUriFromBoth()
    {
        $serverRequest = $this->factory->factory([
            'HTTP_HOST' => 'localhost:8002', // more
            'SERVER_NAME' => '0.0.0.0',
            'SERVER_PORT' => '8002',
            'REQUEST_URI' => '/abk?sdnkf',
        ], [], [], [], []);

        $this->assertEquals('http://localhost:8002/abk?sdnkf', $serverRequest->getUri()->__toString());
    }

    public function testGetJsonParsedBody()
    {
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn('{"hello":[1,2,3,4,5]}');

        $serverRequest = $this->factory->factory([
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], [], [], [], [], $body);

        $this->assertEquals([
            'hello' => [1,2,3,4,5]
        ], $serverRequest->getParsedBody());
    }

    public function testGetJsonParsedBodyWithCharsetHeader()
    {
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn('{"hello":[1,2,3,4,5]}');

        $serverRequest = $this->factory->factory([
            'HTTP_CONTENT_TYPE' => 'application/json;charset=UTF-8',
        ], [], [], [], [], $body);

        $this->assertEquals([
            'hello' => [1,2,3,4,5]
        ], $serverRequest->getParsedBody());
    }
}
