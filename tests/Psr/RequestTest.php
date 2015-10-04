<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\UriInterface;
use Wandu\Http\Psr\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Psr\Request */
    protected $request;

    public function setUp()
    {
        $this->request = new Request();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testConstructor()
    {
        $mockUri = Mockery::mock(UriInterface::class);

        $mockUri->shouldReceive('getPath')->andReturn('');
        $mockUri->shouldReceive('getQuery')->andReturn('');

        $request = new Request('get', $mockUri);

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
        $this->assertNotSame($this->request, $this->request->withRequestTarget('/abc/def'));
        $this->assertEquals('/abc/def', $this->request->withRequestTarget('/abc/def')->getRequestTarget());
    }

    public function testWithMethod()
    {
        $this->assertNotSame($this->request, $this->request->withMethod('post'));
        $this->assertEquals('POST', $this->request->withMethod('post')->getMethod());

        try {
            $this->request->withMethod([]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Unsupported HTTP method. It must be a string.', $e->getMessage());
        }
        try {
            $this->request->withMethod('UNKNOWN');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Unsupported HTTP method. "UNKNOWN" provided.', $e->getMessage());
        }
    }

    public function testWithUri()
    {
        $mockUri = Mockery::mock(UriInterface::class);

        $this->assertNotSame($this->request, $this->request->withUri($mockUri, true));
        $this->assertSame($mockUri, $this->request->withUri($mockUri, true)->getUri());
    }
}
