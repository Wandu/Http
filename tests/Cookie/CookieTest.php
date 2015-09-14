<?php
namespace Wandu\Http\Cookie;

use DateTime;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Mockery;

class CookieTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        try {
            new Cookie('');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The cookie name cannot be empty.', $e->getMessage());
        }
        try {
            new Cookie(',');
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The cookie name "," contains invalid characters.', $e->getMessage());
        }
    }

    public function testDeleteCookie()
    {
        $this->assertEquals(
            "hello=deleted; Expires=Thursday, 01-Jan-1970 00:00:00 GMT; Path=/; HttpOnly",
            (new Cookie('hello'))->__toString()
        );
    }

    public function testSetCookie()
    {
        $this->assertEquals(
            "hello=world; Path=/; HttpOnly",
            (new Cookie('hello', 'world'))->__toString()
        );
    }

    public function testSetCookieWithExpireTime()
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp(10);
        $this->assertEquals(
            "hello=world; Expires=Thursday, 01-Jan-1970 00:00:10 GMT; Path=/; HttpOnly",
            (new Cookie('hello', 'world', $dateTime))->__toString()
        );
    }

    public function testSetCookieWithMeta()
    {
        $this->assertEquals(
            "hello=world; Path=/hello; Domain=blog.wani.kr; Secure",
            (new Cookie('hello', 'world', null, '/hello', 'blog.wani.kr', true, false))->__toString()
        );
    }

    public function testGetMetaData()
    {
        $cookie = new Cookie('hello', 'world', null, '/hello', 'blog.wani.kr', true, false);

        $this->assertEquals('hello', $cookie->getName());
        $this->assertEquals('world', $cookie->getValue());
        $this->assertNull($cookie->getExpire());
        $this->assertEquals('/hello', $cookie->getPath());
        $this->assertEquals('blog.wani.kr', $cookie->getDomain());

        $this->assertTrue($cookie->isSecure());
        $this->assertFalse($cookie->isHttpOnly());
    }
}
