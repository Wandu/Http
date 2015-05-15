<?php
namespace Wandu\Http;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testConstructor()
    {
        $mockUri = Mockery::mock(UriInterface::class);
        $mockUri->shouldReceive('getPath')->andReturn('');
        $mockUri->shouldReceive('getQuery')->andReturn('');

        $request = new Request($mockUri, 'get');

        $this->assertEquals('/', $request->getRequestTarget());
        $this->assertEquals('GET', $request->getMethod());
        $this->assertSame($mockUri, $request->getUri());

        $request = new Request();

        $this->assertEquals('/', $request->getRequestTarget());
        $this->assertEquals(null, $request->getMethod());
        $this->assertSame(null, $request->getUri());
    }

    public function testWithRequestTarget()
    {
        $request = new Request();

        $this->assertNotSame($request, $request->withRequestTarget('/abc/def'));
        $this->assertEquals('/abc/def', $request->withRequestTarget('/abc/def')->getRequestTarget());
    }

    public function testWithMethod()
    {
        $request = new Request();

        $this->assertNotSame($request, $request->withMethod('post'));
        $this->assertEquals('POST', $request->withMethod('post')->getMethod());

        try {
            $request->withMethod([]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Unsupported HTTP method. It must be a string.', $e->getMessage());
        }
        try {
            $request->withMethod('UNKNOWN');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Unsupported HTTP method. "UNKNOWN" provided.', $e->getMessage());
        }
    }

    public function testWithUri()
    {
        $mockUri = Mockery::mock(UriInterface::class);

        $request = new Request();

        $this->assertNotSame($request, $request->withUri($mockUri, true));
        $this->assertSame($mockUri, $request->withUri($mockUri, true)->getUri());
    }
}
