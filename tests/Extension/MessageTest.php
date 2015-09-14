<?php
namespace Wandu\Http\Extension;

use Psr\Http\Message\StreamInterface;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Cookie\CookieJar;
use Wandu\Http\MessageTest as HttpMessageTest;
use Mockery;
use Wandu\Http\Session\Session;

class MessageTest extends HttpMessageTest
{
    /** @var \Wandu\Http\Contracts\Extension\MessageInterface */
    protected $message;

    /** @var \Wandu\Http\Contracts\Extension\MessageInterface */
    protected $messageWithHeader;

    /** @var \Wandu\Http\Contracts\Extension\MessageInterface */
    protected $messageWithBody;

    public function setUp()
    {
        $mockBody = Mockery::mock(StreamInterface::class);

        $this->message = (new Message())->withProtocolVersion('1.0');
        $this->messageWithHeader = $this->message->withHeader('Test', ['Hello', 'World!']);
        $this->messageWithBody = $this->message->withBody($mockBody);
    }

    public function testGetNullCookieJar()
    {
        $this->assertEquals(new CookieJar(), $this->message->getCookieJar()); // return null
    }

    public function testWithCookieJar()
    {
        $mockCookieJar = Mockery::mock(CookieJarInterface::class);

        $this->assertNotSame($this->message, $this->message->withCookieJar($mockCookieJar));

        $this->assertSame($mockCookieJar, $this->message->withCookieJar($mockCookieJar)->getCookieJar());
    }

    public function testGetNullSession()
    {
        $this->assertEquals(new Session(), $this->message->getSession()); // return null
    }

    public function testWithSession()
    {
        $mockSession = Mockery::mock(SessionInterface::class);

        $this->assertNotSame($this->message, $this->message->withSession($mockSession));

        $this->assertSame($mockSession, $this->message->withSession($mockSession)->getSession());
    }
}
