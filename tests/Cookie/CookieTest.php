<?php
namespace Wandu\Http\Cookie;

use DateTime;
use PHPUnit_Framework_TestCase;
use Mockery;

class CookieTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        date_default_timezone_set('Asia/Seoul');
    }

    public function testMany()
    {
        $this->assertEquals(
            "hello=deleted; Expires=Thursday, 01-Jan-1970 00:00:00 GMT; Path=/; HttpOnly",
            (new Cookie('hello'))->__toString()
        );
        $this->assertEquals(
            "hello=world; Path=/; HttpOnly",
            (new Cookie('hello', 'world'))->__toString()
        );
        $dateTime = new DateTime();
        $dateTime->setTimestamp(10);
        $this->assertEquals(
            "hello=world; Expires=Thursday, 01-Jan-1970 00:00:10 GMT; Path=/; HttpOnly",
            (new Cookie('hello', 'world', $dateTime))->__toString()
        );
        $this->assertEquals(
            "hello=world; Path=/hello; Domain=blog.wani.kr; Secure",
            (new Cookie('hello', 'world', null, '/hello', 'blog.wani.kr', true, false))->__toString()
        );
    }
}
