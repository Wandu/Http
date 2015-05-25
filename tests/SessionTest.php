<?php
namespace Wandu\Session;

use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testSessionFromReset()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getCookieParams')->andReturn([]);

        $mockSession = Mockery::mock(SessionInterface::class);
        $mockSession->shouldReceive('set')->with('hello', 'world');
        $mockSession->shouldReceive('get')->with('blabla')->andReturn(null);

        $mockProvider = Mockery::mock(ProviderInterface::class);
        $mockProvider->shouldReceive('getSession')->andReturn($mockSession);

        $session = new Session('PHPSESSID', $mockRequest, $mockProvider);
        $session->set('hello', 'world');

        $this->assertNull($session->get('blabla'));
        $this->assertTrue($session->isReset());
    }

    public function testSessionFromCookie()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getCookieParams')->andReturn([
            'PHPSESSID' => 'aaaa1111bbbb2222'
        ]);

        $mockSession = Mockery::mock(SessionInterface::class);
        $mockSession->shouldReceive('get')->with('blabla')->andReturn('world~~~?');

        $mockProvider = Mockery::mock(ProviderInterface::class);
        $mockProvider->shouldReceive('getSession')->andReturn($mockSession);

        $session = new Session('PHPSESSID', $mockRequest, $mockProvider);

        $this->assertEquals('aaaa1111bbbb2222', $session->getId());
        $this->assertEquals('world~~~?', $session->get('blabla'));
        $this->assertFalse($session->isReset());
    }

    public function testResponseApplyWithReset()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getCookieParams')->andReturn([]);

        $mockSession = Mockery::mock(SessionInterface::class);

        $mockProvider = Mockery::mock(ProviderInterface::class);
        $mockProvider->shouldReceive('getSession')->andReturn($mockSession);

        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockResponse->shouldReceive('withHeader')
            ->with('Set-Cookie', "#PHPSESSID\\=[a-f0-9]*#")->andReturn(Mockery::self());

        $session = new Session('PHPSESSID', $mockRequest, $mockProvider);

        $this->assertInstanceOf(ResponseInterface::class, $session->applyResponse($mockResponse));
    }


    public function testResponseApply()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getCookieParams')->andReturn([
            'PHPSESSID' => 'aaaa1111bbbb2222'
        ]);

        $mockSession = Mockery::mock(SessionInterface::class);

        $mockProvider = Mockery::mock(ProviderInterface::class);
        $mockProvider->shouldReceive('getSession')->andReturn($mockSession);

        $mockResponse = Mockery::mock(ResponseInterface::class);

        $session = new Session('PHPSESSID', $mockRequest, $mockProvider);

        $this->assertInstanceOf(ResponseInterface::class, $session->applyResponse($mockResponse));
    }
}
