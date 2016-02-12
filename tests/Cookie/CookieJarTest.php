<?php
namespace Wandu\Http\Cookie;

use PHPUnit_Framework_TestCase;
use Mockery;
use Traversable;
use Wandu\Http\Contracts\ParameterInterfaceTestTrait;

class CookieJarTest extends PHPUnit_Framework_TestCase
{
    use ParameterInterfaceTestTrait;

    /** @var \Wandu\Http\Cookie\CookieJar */
    private $cookies;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param1;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param2;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param3;

    public function setUp()
    {
        $this->param1 = new CookieJar([
            'string' => 'string!',
            'number' => '10',
        ]);
        $this->param2 = new CookieJar([
            'null' => null,
        ]);

        $this->param3 = new CookieJar([
            'string1' => 'string 1!',
            'string2' => 'string 2!',
        ], new CookieJar([
            'string1' => 'string 1 fallback!',
            'fallback' => 'fallback!',
        ]));

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

    public function testArrayAccess()
    {
        $this->assertSame($this->cookies->get('user'), $this->cookies['user']);
        $this->assertSame($this->cookies->get('unknown'), $this->cookies['unknown']);

        $this->assertSame($this->cookies->has('user'), isset($this->cookies['user']));
        $this->assertSame($this->cookies->has('unknown'), isset($this->cookies['unknown']));

        $this->assertFalse($this->cookies->has('added'));
        $this->cookies['added'] = 'added!';
        $this->assertTrue($this->cookies->has('added'));

        $this->assertTrue($this->cookies->has('user'));
        unset($this->cookies['user']);
        $this->assertFalse($this->cookies->has('user'));
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
