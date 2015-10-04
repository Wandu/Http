<?php
namespace Wandu\Http\Psr;

use Mockery;
use PHPUnit_Framework_TestCase;

class MessageTest extends PHPUnit_Framework_TestCase
{
     use MessageTestTrait;

    public function setUp()
    {
        $this->message = new Message("1.0");
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
