<?php
namespace Wandu\Http\Cookie;

use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\ResponseInterface;

class CookieJarTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Cookie\CookieJar */
    private $cookies;

    public function setUp()
    {
        $this->cookies = new CookieJar([
            'user' => '0000-1111-2222-3333',
        ]);
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
}
