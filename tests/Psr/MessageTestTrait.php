<?php
namespace Wandu\Http\Psr;

use Mockery;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait MessageTestTrait
{
    /** @var \Psr\Http\Message\MessageInterface */
    protected $message;

    public function testProtocolVersion()
    {
        $message = $this->message->withProtocolVersion('1.0');

        // The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
        $this->assertEquals("1.0", $message->getProtocolVersion());

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // new protocol version.
        $this->assertNotSame($message, $message->withProtocolVersion("1.0"));

        $this->assertEquals("1.1", $message->withProtocolVersion("1.1")->getProtocolVersion());
    }

    public function testGetHeaders()
    {
        $message = $this->message;
        $messageWithHeader = $message
            ->withHeader('Test', ['Hello', 'World!']);

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
        $this->assertEquals([], $message->getHeaders());

        $this->assertEquals(['Test' => ['Hello', 'World!']], $messageWithHeader->getHeaders());
    }

    public function testHasHeader()
    {
        $message = $this->message;
        $messageWithHeader = $message
            ->withHeader('Test', ['Hello', 'World!']);

        // Checks if a header exists by the given case-insensitive name.
        $this->assertTrue($messageWithHeader->hasHeader('test'));
        $this->assertTrue($messageWithHeader->hasHeader('teST'));

        $this->assertFalse($messageWithHeader->hasHeader('unknown'));
    }

    public function testGetHeader()
    {
        $message = $this->message;
        $messageWithHeader = $message
            ->withHeader('Test', ['Hello', 'World!']);

        // This method returns an array of all the header values of the given
        // case-insensitive header name.
        $this->assertEquals(['Hello', 'World!'], $messageWithHeader->getHeader('test'));
        $this->assertEquals(['Hello', 'World!'], $messageWithHeader->getHeader('tESt'));

        // If the header does not appear in the message, this method MUST return an
        // empty array.
        $this->assertEquals([], $messageWithHeader->getHeader('unknown'));
    }

    public function testGetHeaderLine()
    {
        $message = $this->message;
        $messageWithHeader = $message
            ->withHeader('Test', ['Hello', 'World!']);

        // This method returns all of the header values of the given
        // case-insensitive header name as a string concatenated together using
        // a comma.
        $this->assertEquals('Hello,World!', $messageWithHeader->getHeaderLine('test'));
        $this->assertEquals('Hello,World!', $messageWithHeader->getHeaderLine('tESt'));

        // NOTE: Not all header values may be appropriately represented using
        // comma concatenation. For such headers, use getHeader() instead
        // and supply your own delimiter when concatenating.

        // If the header does not appear in the message, this method MUST return
        // an empty string.
        $this->assertEquals('', $messageWithHeader->getHeaderLine('unknown'));
    }

    public function testWithHeader()
    {
        $message = $this->message;
        $messageWithHeader = $message
            ->withHeader('Test', ['Hello', 'World!']);

        // While header names are case-insensitive, the casing of the header will
        // be preserved by this function, and returned from getHeaders().
        $this->assertNotSame($messageWithHeader, $messageWithHeader->withHeader('test', 'blabla'));

        try {
            $messageWithHeader->withHeader('test', [[], 'un..']);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid header value. It must be a string or array of strings.', $e->getMessage());
        }
        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // new and/or updated header and value.

        $this->assertEquals(['blabla'], $messageWithHeader->withHeader('other', 'blabla')->getHeader('other'));
        $this->assertEquals(
            ['blabla', 'what'],
            $messageWithHeader->withHeader('other', ['blabla', 'what'])->getHeader('other')
        );

        $this->assertEquals(
            ['Hello', 'World!'],
            $messageWithHeader->withHeader('other', 'blabla')->getHeader('test')
        );

        $this->assertEquals(['blabla'], $messageWithHeader->withHeader('test', 'blabla')->getHeader('test'));
        $this->assertEquals(
            ['blabla', 'what'],
            $messageWithHeader->withHeader('test', ['blabla', 'what'])->getHeader('test')
        );
    }

    public function testWithAddedHeader()
    {
        $message = $this->message;
        $messageWithHeader = $message
            ->withHeader('Test', ['Hello', 'World!']);

        // Existing values for the specified header will be maintained. The new
        // value(s) will be appended to the existing list. If the header did not
        // exist previously, it will be added.
        $this->assertNotSame($messageWithHeader, $messageWithHeader->withAddedHeader('test', 'blabla'));

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // new header and/or value.
        $this->assertEquals(
            ['Hello', 'World!', 'blabla'],
            $messageWithHeader->withAddedHeader('test', 'blabla')->getHeader('test')
        );

        $this->assertEquals([
            'Test' => ['Hello', 'World!'],
            'other' => ['blabla', 'what']
        ], $messageWithHeader->withAddedHeader('other', ['blabla', 'what'])->getHeaders());
    }

    public function testWithoutHeader()
    {
        $message = $this->message;
        $messageWithHeader = $message
            ->withHeader('Test', ['Hello', 'World!']);

        // Header resolution MUST be done without case-sensitivity.
        $this->assertNotSame($messageWithHeader, $messageWithHeader->withoutHeader('foo'));

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that removes
        // the named header.
        $this->assertEquals([
            'Test' => ['Hello', 'World!']
        ], $messageWithHeader->withoutHeader('other')->getHeaders());

        $this->assertEquals([
        ], $messageWithHeader->withoutHeader('test')->getHeaders());
    }

    public function testWithBody()
    {
        $mockBody = Mockery::mock(StreamInterface::class);
        $messageWithBody = $this->message->withBody($mockBody);

        $this->assertNull($this->message->getBody()); // return null

        // The body MUST be a StreamInterface object.
        $this->assertNotSame($this->message, $messageWithBody);

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return a new instance that has the
        // new body stream.
        $this->assertInstanceOf(StreamInterface::class, $messageWithBody->getBody());
        $this->assertSame($mockBody, $messageWithBody->getBody());
    }
}
