<?php
namespace Wandu\Http\Cookie;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;

class CookieApplierTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $jar = new CookieJar([
            'hello' => 'world',
            'override' => 'current'
        ]);
        $jar->set('override', 'replaced');
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockResponse->shouldReceive('withAddedHeader')->once()
            ->with('Set-Cookie', 'override=replaced; Path=/; HttpOnly')->andReturn(Mockery::self());

        $applier = new CookieApplier($jar);
        $applier->apply($mockResponse);
    }
}
