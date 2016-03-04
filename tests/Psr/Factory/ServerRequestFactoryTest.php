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

    public function testGetFromSocketBody()
    {
        $body = <<<HTTP
GET /favicon.ico HTTP/1.1
Host: localhost
Connection: keep-alive
Pragma: no-cache
Cache-Control: no-cache
User-Agent: Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36
Referer: http://localhost/
Cookie: FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3


HTTP;
        $body = str_replace("\n", "\r\n", $body);

        $request = $this->factory->fromSocketBody($body);

        $this->assertEquals('1.1', $request->getProtocolVersion());
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('http://localhost/favicon.ico', $request->getUri()->__toString());

        $this->assertEquals('localhost', $request->getHeaderLine('host'));
        $this->assertEquals('keep-alive', $request->getHeaderLine('connection'));
        $this->assertEquals('no-cache', $request->getHeaderLine('cache-control'));
        $this->assertEquals(
            'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36',
            $request->getHeaderLine('user-agent')
        );
        $this->assertEquals('http://localhost/', $request->getHeaderLine('referer'));

        $this->assertEquals('http://localhost/', $request->getHeaderLine('referer'));

        $this->assertEquals(
            'FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
            $request->getHeaderLine('cookie')
        );
    }
}
