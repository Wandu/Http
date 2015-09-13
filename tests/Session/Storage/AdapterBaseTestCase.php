<?php
namespace Wandu\Http\Session\Storage;

use PHPUnit_Framework_TestCase;
use Mockery;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Session\Session;

abstract class AdapterBaseTestCase extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Session\SessionAdapterInterface */
    protected $adapter;

    public function tearDown()
    {
        Mockery::close();
    }

    public function testEmptySession()
    {
        if (!isset($this->adapter)) {
            $this->markTestSkipped('there is no adapter! :-)');
        }
        $session = $this->adapter->read($this->getRandomSessionId());

        $this->assertInstanceOf(SessionInterface::class, $session);
        $this->assertEquals([], $session->toArray());
    }

    public function testWriteSession()
    {
        if (!isset($this->adapter)) {
            $this->markTestSkipped('there is no adapter! :-)');
        }
        $sessionId = $this->getRandomSessionId();

        $session = new Session();
        $session['hello'] = 'world';
        $session['what'] = "um..";

        // write
        $this->adapter->write($sessionId, $session);

        // then data
        $this->assertEquals([
            'hello' => 'world',
            'what' => 'um..'
        ], $this->adapter->read($sessionId)->toArray());

        // destroy
        $this->adapter->destroy($sessionId);

        // then blank
        $this->assertEquals([], $this->adapter->read($sessionId)->toArray());
    }

    /**
     * @return string
     */
    protected function getRandomSessionId()
    {
        return sha1(uniqid());
    }
}
