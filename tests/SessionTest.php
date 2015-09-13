<?php
namespace Wandu\Session;

use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testSessionFromNullCookie()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getCookieParams')->andReturn([]);

        $mockHandler = Mockery::mock(StorageAdapterInterface::class);
        $mockHandler->shouldReceive('read')->with(Mockery::any())->andReturn([]);

        $manager = new Manager('PHPSESSID', $mockHandler);

        $storage = $manager->readFromRequest($mockRequest);
        $this->assertInstanceOf(Storage::class, $storage);
        $this->assertEquals([], $storage->toArray());
        $this->assertTrue($manager->isReset());
    }

    public function testSessionFromCookie()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getCookieParams')->andReturn([
            'PHPSESSID' => 'aaaa1111bbbb2222'
        ]);

        $mockHandler = Mockery::mock(StorageAdapterInterface::class);
        $mockHandler->shouldReceive('read')->with('aaaa1111bbbb2222')->andReturn([
            'abc' => 'def'
        ]);

        $manager = new Manager('PHPSESSID', $mockHandler);

        $this->assertEquals(['abc' => 'def'], $manager->readFromRequest($mockRequest)->toArray());
        $this->assertEquals('aaaa1111bbbb2222', $manager->getId());
        $this->assertFalse($manager->isReset());
    }

    public function testWriteToResponseWithReset()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getCookieParams')->andReturn([]);

        $mockHandler = Mockery::mock(StorageAdapterInterface::class);
        $mockHandler->shouldReceive('read')->andReturn(['hello' => 'world']);
        $mockHandler->shouldReceive('write')->andReturn([
            'hello' => 'world',
            'blabla' => 'added'
        ]);

        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockResponse->shouldReceive('withHeader')
            ->with('Set-Cookie', "#PHPSESSID\\=[a-f0-9]*#")->andReturn(Mockery::self());

        $session = new Manager('PHPSESSID', $mockHandler);

        $storage = $session->readFromRequest($mockRequest);
        $storage->set('blabla', 'added');

        $this->assertInstanceOf(ResponseInterface::class, $session->writeToResponse($mockResponse, $storage));
    }


    public function testResponseApply()
    {
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getCookieParams')->andReturn([
            'PHPSESSID' => 'aaaa1111bbbb2222'
        ]);

        $mockHandler = Mockery::mock(StorageAdapterInterface::class);
        $mockHandler->shouldReceive('read')->andReturn(['foo' => 'foo 1']);
        $mockHandler->shouldReceive('write')->andReturn([]);

        $mockResponse = Mockery::mock(ResponseInterface::class);

        $session = new Manager('PHPSESSID', $mockHandler);

        $storage = $session->readFromRequest($mockRequest);

        $storage->offsetUnset('foo');

        $this->assertInstanceOf(ResponseInterface::class, $session->writeToResponse($mockResponse, $storage));
    }
}
