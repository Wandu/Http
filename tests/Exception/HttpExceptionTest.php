<?php
namespace Wandu\Http\Exception;

use Mockery;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Wandu\Http\Psr\Stream\StringStream;
use RuntimeException;

class HttpExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testGetDefaultValues()
    {
        $httpException = new HttpException();

        $this->assertSame(500, $httpException->getStatusCode());
        $this->assertSame("Internal Server Error", $httpException->getReasonPhrase());


        $httpException = new HttpException(500, "Hello World?");

        $this->assertSame(500, $httpException->getStatusCode());
        $this->assertSame("Hello World?", $httpException->getReasonPhrase());
    }

    public function testCannotCallWithMethods()
    {
        $httpException = new HttpException();

        try {
            $httpException->withBody(new StringStream());
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change body in HttpException.', $e->getMessage());
        }

        try {
            $httpException->withHeader('content-type', 'application/json');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change header in HttpException.', $e->getMessage());
        }

        try {
            $httpException->withAddedHeader('content-type', 'application/json');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change header in HttpException.', $e->getMessage());
        }

        try {
            $httpException->withoutHeader('content-type');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change header in HttpException.', $e->getMessage());
        }

        try {
            $httpException->withProtocolVersion('2.0');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change protocolVersion in HttpException.', $e->getMessage());
        }

        try {
            $httpException->withStatus(404, 'what..');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('cannot change status in HttpException.', $e->getMessage());
        }
    }

    public function testToRespose()
    {
        $httpException = new HttpException();

        $response = $httpException->toResponse();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('Internal Server Error', $response->getReasonPhrase());
        $this->assertNull($response->getBody());
        $this->assertSame('1.1', $response->getProtocolVersion());
        $this->assertEquals([], $response->getHeaders());



        $httpException = new HttpException(
            400,
            'other reason-phrase',
            new StringStream(),
            ['content-type' => 'application/json'],
            '1.0'
        );

        $response = $httpException->toResponse();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('other reason-phrase', $response->getReasonPhrase());
        $this->assertInstanceOf(StringStream::class, $response->getBody());
        $this->assertSame('1.0', $response->getProtocolVersion());
        $this->assertEquals(['content-type' => 'application/json'], $response->getHeaders());
    }
}
