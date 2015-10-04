<?php
namespace Wandu\Http\Psr;

use PHPUnit_Framework_TestCase;
use Mockery;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

trait ResponseTestTrait
{
    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    public function testWithStatusFail()
    {
        try {
            $this->response->withStatus(9999);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid status code "9999".', $e->getMessage());
        }
        try {
            $this->response->withStatus([]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid status code. It must be a 3-digit integer.', $e->getMessage());
        }
    }

    public function testWithStatus()
    {
        $response = $this->response;
        $responseWithCode = $response->withStatus(201);
        $responseWithPhrase = $response->withStatus(201, 'What');

        $response = new Response();
        $this->assertNotSame($response, $response->withStatus(200));

        // If no reason phrase is specified, implementations MAY choose to default
        // to the RFC 7231 or IANA recommended reason phrase for the response's
        // status code.
        $this->assertEquals(201, $responseWithCode->getStatusCode());
        $this->assertEquals('Created', $responseWithCode->getReasonPhrase());

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // updated status and reason phrase.
        $this->assertEquals(201, $responseWithPhrase->getStatusCode());
        $this->assertEquals('What', $responseWithPhrase->getReasonPhrase());
    }

    public function testGetReasonPhrase()
    {
        $response = $this->response;
        $responseWithCode = $response->withStatus(201);
        $responseWithPhrase = $response->withStatus(201, 'Created !!');

        // Because a reason phrase is not a required element in a response
        // status line, the reason phrase value MAY be null. Implementations MAY
        // choose to return the default RFC 7231 recommended reason phrase (or those
        // listed in the IANA HTTP Status Code Registry) for the response's
        // status code.
        $this->assertEquals('OK', $response->getReasonPhrase());

        $this->assertEquals('Created', $responseWithCode->getReasonPhrase());

        $this->assertEquals('Created !!', $responseWithPhrase->getReasonPhrase());
    }
}
