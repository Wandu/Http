<?php
namespace Wandu\Http\Cookie;

use PHPUnit_Framework_TestCase;
use Mockery;

class CookieTest extends PHPUnit_Framework_TestCase
{
    public function testMany()
    {
        $this->assertEquals(
            "hello=deleted; Expires=Thu, 01-Jan-1970 00:00:00 GMT; Path=/; HttpOnly",
            (new Cookie('hello'))->__toString()
        );
        $this->assertEquals(
            "hello=world; Path=/; HttpOnly",
            (new Cookie('hello', 'world'))->__toString()
        );
        $this->assertEquals(
            "hello=world; Expires=Thu, 01-Jan-1970 00:00:00 GMT; Path=/; HttpOnly",
            (new Cookie('hello', 'world', 0))->__toString()
        );
        $this->assertEquals(
            "hello=world; Path=/hello; Domain=blog.wani.kr; Secure",
            (new Cookie('hello', 'world', null, '/hello', 'blog.wani.kr', true, false))->__toString()
        );
    }
}
