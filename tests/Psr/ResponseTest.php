<?php
namespace Wandu\Http\Psr;

use PHPUnit_Framework_TestCase;
use Mockery;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    use ResponseTestTrait, MessageTestTrait;

    public function setUp()
    {
        $this->response = $this->message = new Response();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
