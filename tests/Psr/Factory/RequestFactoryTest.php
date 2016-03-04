<?php
namespace Wandu\Http\Psr\Factory;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\StreamInterface;

class RequestFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Psr\Factory\RequestFactory */
    protected $factory;

    public function setUp()
    {
        $this->factory = new RequestFactory();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateRequest()
    {
        $request = $this->factory->createRequest([
            'GET /hello/world HTTP/1.1',
            'Host: localhost',
            'User-Agent: Safari/537.36',
            'Referer: http://localhost/',
            'Cookie: FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
        ]);

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('http://localhost/hello/world', $request->getUri()->__toString());
        $this->assertEquals('1.1', $request->getProtocolVersion());

        $this->assertEquals('localhost', $request->getHeaderLine('host'));
        $this->assertEquals('Safari/537.36', $request->getHeaderLine('user-agent'));
        $this->assertEquals('http://localhost/', $request->getHeaderLine('referer'));
        $this->assertEquals(
            'FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
            $request->getHeaderLine('cookie')
        );

        $request = $this->factory->createRequest([
            'GET /hello/world HTTP/1.0',
            'Host: localhost',
            'User-Agent: Safari/537.36',
            'Referer: http://localhost/',
            'Cookie: FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
        ]);

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('http://localhost/hello/world', $request->getUri()->__toString());
        $this->assertEquals('1.0', $request->getProtocolVersion());

        $this->assertEquals('localhost', $request->getHeaderLine('host'));
        $this->assertEquals('Safari/537.36', $request->getHeaderLine('user-agent'));
        $this->assertEquals('http://localhost/', $request->getHeaderLine('referer'));
        $this->assertEquals(
            'FOO=135050505050; BAR=1; PHPSESSID=djiar0p36a1nhrc3j6pd6r0gp3',
            $request->getHeaderLine('cookie')
        );
    }

    public function testCheckUriWhenCreateRequest()
    {
        $request = $this->factory->createRequest([
            'GET /hello/world HTTP/1.1',
        ]);

        $this->assertEquals('/hello/world', $request->getUri()->__toString());

        $request = $this->factory->createRequest([
            'GET / HTTP/1.1',
        ]);

        $this->assertEquals('/', $request->getUri()->__toString());

        $request = $this->factory->createRequest([
            'GET / HTTP/1.1',
            'Host: localhost',
        ]);

        $this->assertEquals('http://localhost', $request->getUri()->__toString());
    }
}
