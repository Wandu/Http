<?php
namespace Wandu\Http\Psr;

use Mockery;
use PHPUnit_Framework_TestCase;

class RequestTest extends PHPUnit_Framework_TestCase
{
    use RequestTestTrait, MessageTestTrait;

    public function setUp()
    {
        $this->request = $this->message = new Request();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
