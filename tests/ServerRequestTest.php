<?php
namespace Wandu\Http;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class ServerRequestTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testSimpleConstruct()
    {
        $request = new ServerRequest();

        $this->assertEquals([], $request->getServerParams());
        $this->assertEquals([], $request->getCookieParams());
        $this->assertEquals([], $request->getQueryParams());
        $this->assertEquals([], $request->getUploadedFiles());
        $this->assertEquals([], $request->getParsedBody());
        $this->assertEquals([], $request->getAttributes());

        // message
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $this->assertEquals([], $request->getHeaders());
        $this->assertInstanceOf(Stream::class, $request->getBody());

        // request
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals(null, $request->getUri());
        $this->assertEquals('/', $request->getRequestTarget());
    }

    public function testConstructWithSuccess()
    {
        $mockFile = Mockery::mock(UploadedFileInterface::class);
        $mockUri = Mockery::mock(UriInterface::class);
        $mockUri->shouldReceive('getPath')->andReturn('/abc/def');
        $mockUri->shouldReceive('getQuery')->andReturn('hello=world');

        $request = new ServerRequest(
            [
                'SERVER_SOFTWARE' => 'PHP 5.6.8 Development Server',
                'SERVER_PROTOCOL' => 'HTTP/1.1',
                'SERVER_NAME' => '0.0.0.0',
                'SERVER_PORT' => '8002',
                'REQUEST_URI' => '/',
                'REQUEST_METHOD' => 'POST',
                'PHP_SELF' => '/index.php',
                'HTTP_HOST' => 'localhost:8002',
                'HTTP_CONNECTION' => 'keep-alive',
                'HTTP_CONTENT_LENGTH' => '56854',
                'HTTP_PRAGMA' => 'no-cache',
                'HTTP_CACHE_CONTROL' => 'no-cache',
                'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'HTTP_ORIGIN' => 'http://localhost:8002',
                'HTTP_USER_AGENT' => 'Mozilla/5.0',
                'HTTP_COOKIE' => 'PHPSESSID=32eo4tk9dcaacb2f3hqg0s6s54',
                'REQUEST_TIME_FLOAT' => 1431675149.3160019,
                'REQUEST_TIME' => 1431675149,
            ],
            ['PHPSESSID' => '32eo4tk9dcaacb2f3hqg0s6s54'],
            [
                'page' => 1,
                'order' => false
            ],
            ['profileImage' => $mockFile],
            ['id' => 'wan2land'],
            ['status' => 'join'],
            '2.0',
            $mockUri
        );
        $this->assertEquals(['PHPSESSID' => '32eo4tk9dcaacb2f3hqg0s6s54'], $request->getCookieParams());
        $this->assertEquals(['page' => 1, 'order' => false], $request->getQueryParams());
        $this->assertEquals(['profileImage' => $mockFile], $request->getUploadedFiles());
        $this->assertEquals(['id' => 'wan2land'], $request->getParsedBody());
        $this->assertEquals(['status' => 'join'], $request->getAttributes());

        // message
        $this->assertEquals('2.0', $request->getProtocolVersion());
        $this->assertEquals([
            'host' => ['localhost:8002'],
            'connection' => ['keep-alive'],
            'content-length' => ['56854'],
            'pragma' => ['no-cache'],
            'cache-control' => ['no-cache'],
            'accept' => ['text/html','application/xhtml+xml','application/xml;q=0.9','image/webp','*/*;q=0.8'],
            'origin' => ['http://localhost:8002'],
            'user-agent' => ['Mozilla/5.0']
        ], $request->getHeaders());
        $this->assertInstanceOf(Stream::class, $request->getBody());

        // request
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals($mockUri, $request->getUri());
        $this->assertEquals('/abc/def?hello=world', $request->getRequestTarget());
    }

    public function testConstructWithFail()
    {
        try {
            new ServerRequest([], [], [], ['hello' => ['world' => new \stdClass()]]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals(
                'Invalid uploaded files value. It must be a array of UploadedFileInterface.',
                $e->getMessage()
            );
        }
    }

    public function testWithCookieParams()
    {
        $request = new ServerRequest();

        $this->assertNotSame($request, $request->withCookieParams([]));
        $this->assertEquals(
            ['other' => 'blabla'],
            $request->withCookieParams(['other' => 'blabla'])->getCookieParams()
        );
    }

    public function testWithQueryParams()
    {
        $request = new ServerRequest();
        $this->assertNotSame($request, $request->withQueryParams([]));
        $this->assertEquals(
            ['other' => 'blabla'],
            $request->withQueryParams(['other' => 'blabla'])->getQueryParams()
        );
    }

    public function testWithUploadedFiles()
    {
        $mockFile = Mockery::mock(UploadedFileInterface::class);

        $request = new ServerRequest();

        $this->assertNotSame($request, $request->withUploadedFiles([]));
        $this->assertEquals(
            ['main' => $mockFile],
            $request->withUploadedFiles(['main' => $mockFile])->getUploadedFiles()
        );
    }

    public function testWithParsedBody()
    {
        $request = new ServerRequest();

        $this->assertNotSame($request, $request->withParsedBody([]));
        $this->assertEquals(
            ['main' => 'hello?'],
            $request->withParsedBody(['main' => 'hello?'])->getParsedBody()
        );
    }

    public function testGetAttribute()
    {
        $request = new ServerRequest([], [], [], [], [], [
            'id' => 'wan2land',
            'status' => 'modify'
        ]);
        $this->assertEquals('wan2land', $request->getAttribute('id'));
        $this->assertEquals('modify', $request->getAttribute('status'));

        $this->assertNull($request->getAttribute('unknown'));
        $this->assertEquals('default', $request->getAttribute('unknown', 'default'));
    }

    public function testWithAttribute()
    {
        $request = new ServerRequest();

        $this->assertNotSame($request, $request->withAttribute('name', 30));
        $this->assertEquals([
            'name' => 30
        ], $request->withAttribute('name', 30)->getAttributes());
    }

    public function testWithoutAttribute()
    {
        $request = new ServerRequest([], [], [], [], [], [
            'id' => 'wan2land',
            'status' => 'modify'
        ]);

        $this->assertNotSame($request, $request->withoutAttribute('id'));
        $this->assertNotSame($request, $request->withoutAttribute('unknown'));

        $this->assertEquals([
            'status' => 'modify'
        ], $request->withoutAttribute('id')->getAttributes());

    }
}
