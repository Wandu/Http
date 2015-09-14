<?php
namespace Wandu\Http\Session;

use DateTime;
use PHPUnit_Framework_TestCase;
use Mockery;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\SessionAdapterInterface;
use Wandu\Http\Contracts\SessionInterface;

class SessionFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Session\SessionFactory */
    protected $factory;

    public function setUp()
    {
        $mockAdapter = Mockery::mock(SessionAdapterInterface::class);
        $mockAdapter->shouldReceive('read')
            ->with('testSessionFromNullCookieKey')->andReturn(new Session([
                'method' => 'testSessionFromNullCookie'
            ]));
        $mockAdapter->shouldReceive('read')
            ->with('testSessionFromCookieKey')->andReturn(new Session([
                'method' => 'testSessionFromCookie'
            ]));
        $this->factory = new SessionFactory($mockAdapter, [
            'name' => 'TestSessionId',
            'timeout' => 1600 // 30min
        ]);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testSessionFromNullCookie()
    {
        $cookieJar = Mockery::mock(CookieJarInterface::class);
        $cookieJar->shouldReceive('has')->once()->andReturn(false);
        $cookieJar->shouldReceive('set')->once()
            ->with('TestSessionId', Mockery::any(), Mockery::type(Datetime::class))->andReturnSelf();
        $cookieJar->shouldReceive('get')->once()
            ->with('TestSessionId')->andReturn('testSessionFromNullCookieKey');

        $storage = $this->factory->fromCookieJar($cookieJar);

        $this->assertInstanceOf(Session::class, $storage);
        $this->assertInstanceOf(SessionInterface::class, $storage);

        $this->assertEquals([
            'method' => 'testSessionFromNullCookie'
        ], $storage->toArray());
    }

    public function testSessionFromCookie()
    {
        $cookieJar = Mockery::mock(CookieJarInterface::class);
        $cookieJar->shouldReceive('has')->once()->andReturn(true);
        $cookieJar->shouldReceive('get')->once()
            ->with('TestSessionId')->andReturn('testSessionFromCookieKey');

        $storage = $this->factory->fromCookieJar($cookieJar);

        $this->assertInstanceOf(Session::class, $storage);
        $this->assertInstanceOf(SessionInterface::class, $storage);

        $this->assertEquals([
            'method' => 'testSessionFromCookie'
        ], $storage->toArray());
    }
}
