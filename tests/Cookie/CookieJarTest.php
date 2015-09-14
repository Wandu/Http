<?php
namespace Wandu\Http\Cookie;

use PHPUnit_Framework_TestCase;
use Mockery;
use Traversable;

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

    public function testSetAndRemove()
    {
        // first
        $this->assertEquals('0000-1111-2222-3333', $this->cookies->get('user'));
        $this->asserttrue($this->cookies->has('user'));

        $this->assertNull($this->cookies->get('_new'));
        $this->assertFalse($this->cookies->has('_new'));

        // set
        $this->cookies->set('_new', "new value!");
        $this->assertEquals('new value!', $this->cookies->get('_new'));
        $this->assertTrue($this->cookies->has('_new'));

        $this->cookies->remove('_new');
        $this->assertNull($this->cookies->get('_new'));
        $this->assertFalse($this->cookies->has('_new'));

        $this->cookies->remove('user');
        $this->assertNull($this->cookies->get('user'));
        $this->assertFalse($this->cookies->has('user'));
    }

    public function testGetIterator()
    {
        $this->assertEquals([], $this->checkCookieAndGetKeys($this->cookies));

        $this->cookies->set('hello', 'world');
        $this->assertEquals(['hello'], $this->checkCookieAndGetKeys($this->cookies));

        // remove also added iterate
        $this->cookies->remove('user');
        $this->cookies->remove('unknown');
        $this->assertEquals(['hello', 'user', 'unknown'], $this->checkCookieAndGetKeys($this->cookies));
    }

    protected function checkCookieAndGetKeys(Traversable $iterator)
    {
        $iterateKeys = [];
        foreach ($iterator as $key => $cookie) {
            $iterateKeys[] = $key;
            $this->assertInstanceOf(Cookie::class, $cookie);
        }
        return $iterateKeys;
    }
}
