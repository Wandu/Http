<?php
namespace Wandu\Http\Issues;

use PHPUnit_Framework_TestCase;
use Wandu\Http\Psr\Request;
use Wandu\Http\Psr\Uri;

class Issue17 extends PHPUnit_Framework_TestCase 
{
    public function testRequest()
    {
        $headers = [
            'Connection' => 'keep-alive',
            'Accept-Encoding' => ['gzip', 'deflate'],
        ];

        $req = new Request('GET', new Uri('http://example.com/index.html'), '1.1', $headers);
        static::assertEquals([
            'Connection' => ['keep-alive', ],
            'Accept-Encoding' => ['gzip', 'deflate'],
        ], $req->getHeaders());
    }
}
