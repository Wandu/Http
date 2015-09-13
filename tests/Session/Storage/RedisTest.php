<?php
namespace Wandu\Http\Session\Storage;

use PHPUnit_Framework_TestCase;
use Mockery;
use Predis\Client;

class RedisTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testA()
    {

//
//    public function testGetSession()
//    {
//        $mockClient = Mockery::mock(Client::class);
//        $mockClient->shouldReceive('get')->with('_WD_aaabbbccc')->andReturn(null);
//
//        $provider = new RedisSessionProvider($mockClient);
//        $this->assertInstanceOf(SessionInterface::class, $provider->getSession('aaabbbccc'));
//    }
//
//    public function testConstructFromNull()
//    {
//        $mockClient = Mockery::mock(Client::class);
//        $mockClient->shouldReceive('get')->with('_WD_aaaa1111')->andReturn(null);
//        $mockClient->shouldReceive('set')->with('_WD_aaaa1111', 'a:2:{s:3:"foo";a:0:{}s:3:"bar";s:5:"hello";}');
//        $session = new RedisSession($mockClient, 'aaaa1111');
//
//        $session->set('foo', []);
//        $session->set('bar', 'hello');
//
//        unset($session);
//    }
//
//    public function testConstructFromSerialize()
//    {
//        $mockClient = Mockery::mock(Client::class);
//        $mockClient->shouldReceive('get')->with('_WD_aaaa1111')
//            ->andReturn('a:2:{s:3:"foo";a:0:{}s:3:"bar";s:5:"hello";}');
//        $mockClient->shouldReceive('set')->with('_WD_aaaa1111', 'a:1:{s:3:"bar";s:5:"hello";}');
//        $session = new RedisSession($mockClient, 'aaaa1111');
//
//        $this->assertEquals([], $session->get('foo'));
//        $this->assertEquals('hello', $session->get('bar'));
//
//        $session->delete('foo');
//
//        unset($session);

//        $file = new FileProvider(__DIR__ . '/sessions');
//        $session = $file->getSession($sessionId);
//
//        $session->set('foo', 'hello');
//        $session->set('what', ['abc', 'def']);
//
//        unset($session);
//
//        $session = $file->getSession($sessionId);
//        $this->assertEquals('hello', $session->get('foo'));
//        $this->assertEquals(['abc', 'def'], $session->get('what'));
//
//        $session->delete('foo');
//
//        $this->assertNull($session->get('foo'));
//        $this->assertEquals(['abc', 'def'], $session->get('what'));
//
//        $session->destroy();
//
//        $this->assertNull($session->get('foo'));
//        $this->assertNull($session->get('what'));
    }
}
