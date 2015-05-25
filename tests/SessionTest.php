<?php
namespace Wandu\Session;

use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\ServerRequestInterface;

class SessionTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getCookieParams')->andReturn([]);

        $mockSession = Mockery::mock(SessionInterface::class);
        $mockSession->shouldReceive('set')->with('hello', 'world');
        $mockSession->shouldReceive('get')->with('blabla')->andReturn('world~~~?');

        $mockProvider = Mockery::mock(ProviderInterface::class);
        $mockProvider->shouldReceive('getSession')->andReturn($mockSession);

        $session = new Session('PHPSESSID', $mockRequest, $mockProvider);
        $session->set('hello', 'world');

        $this->assertEquals('world~~~?', $session->get('blabla'));
    }
}
