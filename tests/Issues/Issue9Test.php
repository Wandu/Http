<?php
namespace Wandu\Http\Issues;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\StreamInterface;
use Wandu\Http\Psr\Factory\ServerRequestFactory;
use Wandu\Http\Psr\Factory\UploadedFileFactory;

class Issue9Test extends PHPUnit_Framework_TestCase
{
    public function provideMethodAndBody()
    {
        $methods = [
            'POST', 'PUT', 'GET', 'DELETE',
        ];

        $contentTypes = [
            [
                'type' => 'application/json',
                'input' => '{"hello":[1,2,3,4,5]}',
                'expected' => [
                    'hello' => [1, 2, 3, 4, 5],
                ],
            ],
            [
                'type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'input' => 'hello%5B%5D=1&hello%5B%5D=2&hello%5B%5D=3&hello%5B%5D=4&hello%5B%5D=5',
                'expected' => [
                    'hello' => [1, 2, 3, 4, 5],
                ],
            ],
        ];

        $providers = [];
        foreach ($methods as $method) {
            foreach ($contentTypes as $contentType) {
                $providers[] = [$method, $contentType];
            }
        }
        return $providers;
    }

    /**
     * @dataProvider provideMethodAndBody
     */
    public function testRequestPost($method, $contentType)
    {
        $servers = [
            'HTTP_CONTENT_TYPE' => $contentType['type'],
            'REQUEST_METHOD' => $method,
        ];

        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn($contentType['input']);

        $factory = new ServerRequestFactory(new UploadedFileFactory());

        // if method === post => php can parsed body!
        $posts = ($method === 'POST' && $contentType['type'] !== 'application/json') ? $contentType['expected'] : [];
        $request = $factory->factory($servers, [], $posts, [], [], $body);
        $this->assertEquals($contentType['expected'], $request->getParsedBody());
    }
}
