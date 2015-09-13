<?php
namespace Wandu\Http\Cookie;

use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CookieJarTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Cookie\CookieJar */
    private $cookies;

    public function setUp()
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getCookieParams')->andReturn([
            'user' => '0000-1111-2222-3333',
        ])->once();
        $this->cookies = new CookieJar($request);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testGet()
    {
        $this->assertEquals('0000-1111-2222-3333', $this->cookies->get('user'));
        $this->assertNull($this->cookies->get('not_exists_key'));
    }

    public function testWithSetCookieHeader()
    {
        $this->cookies->set('wandu', 'abcdefghijklmnopqrstuvwxyz');
        $this->cookies->remove('user');

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('withAddedHeader')
            ->with(
                'Set-Cookie',
                Mockery::anyOf(
                    "wandu=abcdefghijklmnopqrstuvwxyz; Path=/; HttpOnly",
                    "user=deleted; Expires=Thu, 01-Jan-1970 00:00:00 GMT; Path=/; HttpOnly"
                )
            )
            ->twice()->andReturnSelf();

        $this->cookies->withSetCookieHeader($response);
    }
}
