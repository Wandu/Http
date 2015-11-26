<?php
namespace Wandu\Http\Session\Adapter;

use Mockery;
use PHPUnit_Framework_TestCase;
use Predis\Client;

class RedisAdapterTest extends PHPUnit_Framework_TestCase
{
    /** @var \Mockery\Mock */
    protected $client;

    /** @var \Wandu\Http\Session\Adapter\RedisAdapter */
    protected $adapter;

    public function setUp()
    {
        $this->client = Mockery::mock(Client::class);
        $this->adapter = new RedisAdapter($this->client);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testEmptySession()
    {
        $this->client->shouldReceive('get')->andReturn([]);

        if (!isset($this->adapter)) {
            $this->markTestSkipped('there is no adapter! :-)');
        }
        $session = $this->adapter->read(sha1(uniqid()));

        $this->assertEquals([], $session);
    }

    public function testWriteSession()
    {
        if (!isset($this->adapter)) {
            $this->markTestSkipped('there is no adapter! :-)');
        }
        $sessionId = sha1(uniqid());

        $this->client->shouldReceive('set')->with("wandu.http.sess.{$sessionId}", serialize([
            'hello' => 'world',
            'what' => 'um..'
        ]));
        $this->client->shouldReceive('expire')->with("wandu.http.sess.{$sessionId}", 1800);
        $this->client->shouldReceive('del')->with("wandu.http.sess.{$sessionId}");
        $this->client->shouldReceive('get')->andReturn([]);

        // write
        $this->adapter->write($sessionId, [
            'hello' => 'world',
            'what' => 'um..'
        ]);

        // destroy
        $this->adapter->destroy($sessionId);

        // then blank
        $this->assertEquals([], $this->adapter->read($sessionId));
    }
}
