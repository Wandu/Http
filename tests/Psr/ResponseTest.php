<?php
namespace Wandu\Http\Psr;

use PHPUnit_Framework_TestCase;
use Mockery;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ResponseInterface::class, new Response());

        try {
            new Response(9999);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid status code "9999".', $e->getMessage());
        }
        try {
            new Response([]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid status code. It must be a 3-digit integer.', $e->getMessage());
        }
    }

    public function testGetStatusCode()
    {
        // The status code is a 3-digit integer result code of the server's attempt
        // to understand and satisfy the request.
        $response = new Response();
        $this->assertEquals(200, $response->getStatusCode());

        $response = new Response(404);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testWithStatus()
    {
        $response = new Response();
        $this->assertNotSame($response, $response->withStatus(200));

        // If no reason phrase is specified, implementations MAY choose to default
        // to the RFC 7231 or IANA recommended reason phrase for the response's
        // status code.
        $this->assertEquals(201, $response->withStatus(201)->getStatusCode());
        $this->assertEquals('Created', $response->withStatus(201)->getReasonPhrase());

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // updated status and reason phrase.
        $this->assertEquals(201, $response->withStatus(201, 'What')->getStatusCode());
        $this->assertEquals('What', $response->withStatus(201, 'What')->getReasonPhrase());
    }

    public function testGetReasonPhrase()
    {
        // Because a reason phrase is not a required element in a response
        // status line, the reason phrase value MAY be null. Implementations MAY
        // choose to return the default RFC 7231 recommended reason phrase (or those
        // listed in the IANA HTTP Status Code Registry) for the response's
        // status code.
        $response = new Response(200);
        $this->assertEquals('OK', $response->getReasonPhrase());

        $response = new Response(404);
        $this->assertEquals('Not Found', $response->getReasonPhrase());

        $response = new Response(200, 'OK!!!');
        $this->assertEquals('OK!!!', $response->getReasonPhrase());
    }
}
