<?php
namespace Wandu\Http\Psr;

use PHPUnit_Framework_TestCase;
use Mockery;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class MessageTest extends PHPUnit_Framework_TestCase
{
    /** @var \Psr\Http\Message\MessageInterface */
    protected $message;

    /** @var \Psr\Http\Message\MessageInterface */
    protected $messageWithHeader;

    /** @var \Psr\Http\Message\MessageInterface */
    protected $messageWithBody;

    public function setUp()
    {
        $mockBody = Mockery::mock(StreamInterface::class);

        $this->message = new Message("1.0");
        $this->messageWithHeader = new Message("1.0", [
            'Test' => ['Hello', 'World!']
        ]);
        $this->messageWithBody = new Message("1.0", [], $mockBody);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testProtocolVersion()
    {
        $message = $this->message->withProtocolVersion('1.0');

        // The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
        $this->assertEquals("1.0", $this->message->getProtocolVersion());

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // new protocol version.
        $this->assertNotSame($this->message, $this->message->withProtocolVersion("1.0"));

        $this->assertEquals("1.1", $this->message->withProtocolVersion("1.1")->getProtocolVersion());
    }

    public function testGetHeaders()
    {
        //The keys represent the header name as it will be sent over the wire, and
        //each value is an array of strings associated with the header.

        //    // Represent the headers as a string
        //    foreach ($message->getHeaders() as $name => $values) {
        //        echo $name . ": " . implode(", ", $values);
        //    }

        //    // Emit headers iteratively:
        //    foreach ($message->getHeaders() as $name => $values) {
        //        foreach ($values as $value) {
        //            header(sprintf('%s: %s', $name, $value), false);
        //        }
        //    }

        //While header names are not case-sensitive, getHeaders() will preserve the
        //exact case in which headers were originally specified.
        $this->assertEquals([], $this->message->getHeaders());

        $this->assertEquals(['Test' => ['Hello', 'World!']], $this->messageWithHeader->getHeaders());
    }

    public function testHasHeader()
    {
        // Checks if a header exists by the given case-insensitive name.
        $this->assertTrue($this->messageWithHeader->hasHeader('test'));
        $this->assertTrue($this->messageWithHeader->hasHeader('teST'));

        $this->assertFalse($this->messageWithHeader->hasHeader('unknown'));
    }

    public function testGetHeader()
    {
        // This method returns an array of all the header values of the given
        // case-insensitive header name.
        $this->assertEquals(['Hello', 'World!'], $this->messageWithHeader->getHeader('test'));
        $this->assertEquals(['Hello', 'World!'], $this->messageWithHeader->getHeader('tESt'));

        // If the header does not appear in the message, this method MUST return an
        // empty array.
        $this->assertEquals([], $this->messageWithHeader->getHeader('unknown'));
    }

    public function testGetHeaderLine()
    {
        // This method returns all of the header values of the given
        // case-insensitive header name as a string concatenated together using
        // a comma.
        $this->assertEquals('Hello,World!', $this->messageWithHeader->getHeaderLine('test'));
        $this->assertEquals('Hello,World!', $this->messageWithHeader->getHeaderLine('tESt'));

        // NOTE: Not all header values may be appropriately represented using
        // comma concatenation. For such headers, use getHeader() instead
        // and supply your own delimiter when concatenating.

        // If the header does not appear in the message, this method MUST return
        // an empty string.
        $this->assertEquals('', $this->messageWithHeader->getHeaderLine('unknown'));
    }

    public function testWithHeader()
    {
        // While header names are case-insensitive, the casing of the header will
        // be preserved by this function, and returned from getHeaders().
        $this->assertNotSame($this->messageWithHeader, $this->messageWithHeader->withHeader('test', 'blabla'));

        try {
            $this->messageWithHeader->withHeader('test', [[], 'un..']);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid header value. It must be a string or array of strings.', $e->getMessage());
        }
        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // new and/or updated header and value.

        $this->assertEquals(['blabla'], $this->messageWithHeader->withHeader('other', 'blabla')->getHeader('other'));
        $this->assertEquals(
            ['blabla', 'what'],
            $this->messageWithHeader->withHeader('other', ['blabla', 'what'])->getHeader('other')
        );

        $this->assertEquals(
            ['Hello', 'World!'],
            $this->messageWithHeader->withHeader('other', 'blabla')->getHeader('test')
        );

        $this->assertEquals(['blabla'], $this->messageWithHeader->withHeader('test', 'blabla')->getHeader('test'));
        $this->assertEquals(
            ['blabla', 'what'],
            $this->messageWithHeader->withHeader('test', ['blabla', 'what'])->getHeader('test')
        );
    }

    public function testWithAddedHeader()
    {
        // Existing values for the specified header will be maintained. The new
        // value(s) will be appended to the existing list. If the header did not
        // exist previously, it will be added.
        $this->assertNotSame($this->messageWithHeader, $this->messageWithHeader->withAddedHeader('test', 'blabla'));

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // new header and/or value.
        $this->assertEquals(
            ['Hello', 'World!', 'blabla'],
            $this->messageWithHeader->withAddedHeader('test', 'blabla')->getHeader('test')
        );

        $this->assertEquals([
            'Test' => ['Hello', 'World!'],
            'other' => ['blabla', 'what']
        ], $this->messageWithHeader->withAddedHeader('other', ['blabla', 'what'])->getHeaders());
    }

    public function testWithoutHeader()
    {
        // Header resolution MUST be done without case-sensitivity.
        $this->assertNotSame($this->messageWithHeader, $this->messageWithHeader->withoutHeader('foo'));

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that removes
        // the named header.
        $this->assertEquals([
            'Test' => ['Hello', 'World!']
        ], $this->messageWithHeader->withoutHeader('other')->getHeaders());

        $this->assertEquals([
        ], $this->messageWithHeader->withoutHeader('test')->getHeaders());
    }

    public function testGetBody()
    {
        $this->assertNull($this->message->getBody()); // return null

        // Gets the body of the message.
        $this->assertInstanceOf(StreamInterface::class, $this->messageWithBody->getBody());
    }

    public function testWithBody()
    {
        $mockBody = Mockery::mock(StreamInterface::class);

        // The body MUST be a StreamInterface object.
        $this->assertNotSame($this->message, $this->message->withBody($mockBody));

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return a new instance that has the
        // new body stream.
        $this->assertSame($mockBody, $this->message->withBody($mockBody)->getBody());
    }
}
