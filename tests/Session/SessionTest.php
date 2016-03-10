<?php
namespace Wandu\Http\Session;

use PHPUnit_Framework_TestCase;
use Mockery;
use InvalidArgumentException;
use Wandu\Http\Contracts\ParameterInterfaceTestTrait;

class SessionTest extends PHPUnit_Framework_TestCase
{
    use ParameterInterfaceTestTrait;

    /** @var \Wandu\Http\Session\Session */
    private $session;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param1;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param2;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param3;

    public function setUp()
    {
        $this->param1 = new Session(1, [
            'string' => 'string!',
            'number' => '10',
        ]);
        $this->param2 = new Session(1, [
            'null' => null,
        ]);

        $this->param3 = new Session(1, [
            'string1' => 'string 1!',
            'string2' => 'string 2!',
        ], new Session(1, [
            'string1' => 'string 1 fallback!',
            'fallback' => 'fallback!',
        ]));

        $this->session = new Session('namename', [
            'id' => 37,
            'username' => 'wan2land'
        ]);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testInvalidName()
    {
        try {
            $this->session->set(30, 30);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The session name must be string; "30"', $e->getMessage());
        }
        try {
            $this->session->set('', 30);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The session name cannot be empty.', $e->getMessage());
        }
    }

    public function testGet()
    {
        $this->assertEquals(37, $this->session->get('id'));
        $this->assertNull($this->session->get('not_exists_key'));
    }

    public function testSetAndRemove()
    {
        // first
        $this->assertEquals(37, $this->session->get('id'));
        $this->asserttrue($this->session->has('id'));

        $this->assertNull($this->session->get('_new'));
        $this->assertFalse($this->session->has('_new'));

        // set
        $this->session->set('_new', "new value!");
        $this->assertEquals('new value!', $this->session->get('_new'));
        $this->assertTrue($this->session->has('_new'));

        $this->session->remove('_new');
        $this->assertNull($this->session->get('_new'));
        $this->assertFalse($this->session->has('_new'));

        $this->session->remove('id');
        $this->assertNull($this->session->get('id'));
        $this->assertFalse($this->session->has('id'));
    }

    public function testArrayAccess()
    {
        $this->assertSame($this->session->get('id'), $this->session['id']);
        $this->assertSame($this->session->get('unknown'), $this->session['unknown']);

        $this->assertSame($this->session->has('id'), isset($this->session['id']));
        $this->assertSame($this->session->has('unknown'), isset($this->session['unknown']));

        $this->assertFalse($this->session->has('added'));
        $this->session['added'] = 'added!';
        $this->assertTrue($this->session->has('added'));

        $this->assertTrue($this->session->has('id'));
        unset($this->session['id']);
        $this->assertFalse($this->session->has('id'));
    }

    /**
     * @issue #4 add session flash method
     * @ref https://github.com/Wandu/Http/issues/4
     */
    public function testFlash()
    {
        $this->session->flash('flash', 'hello world!');

        $this->assertEquals('hello world!', $this->session->get('flash'));
        $this->assertNull($this->session->get('flash'));
    }

    public function testHasWithFlash()
    {
        $this->session->flash('flash', 'hello world!');

        $this->assertTrue($this->session->has('flash'));
        $this->assertTrue($this->session->has('flash'));
        $this->assertTrue($this->session->has('flash'));

        $this->assertEquals('hello world!', $this->session->get('flash'));

        $this->assertFalse($this->session->has('flash'));
        $this->assertFalse($this->session->has('flash'));
        $this->assertFalse($this->session->has('flash'));

        $this->assertNull($this->session->get('flash'));
    }

    public function testToArrayWithFlash()
    {
        $this->session->flash('flash', 'hello world!');

        $this->assertEquals([
            'flash' => 'hello world!',
            'id' => 37,
            'username' => 'wan2land'
        ], $this->session->toArray());

        $this->assertEquals([
            'id' => 37,
            'username' => 'wan2land'
        ], $this->session->toArray());
    }
}
