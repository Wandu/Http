<?php
namespace Wandu\Http\Issues;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Http\Psr\ServerRequest;

class Issue12Test extends PHPUnit_Framework_TestCase
{
    public function testGetAttribute()
    {
        $request = (new ServerRequest())->withAttribute('null', null);

        $this->assertNull($request->getAttribute('null'));
        $this->assertNull($request->getAttribute('null', 20));
    }
}
