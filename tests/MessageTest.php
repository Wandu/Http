<?php
namespace Wandu\Http;

use PHPUnit_Framework_TestCase;
use Mockery;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class MessageTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetProtocolVersion()
    {
        // The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
        $message = new Message("1.0");
        $this->assertEquals("1.0", $message->getProtocolVersion());

        $message = new Message("1.1");
        $this->assertEquals("1.1", $message->getProtocolVersion());
    }

    public function testWithProtocolVersion()
    {
        // The version string MUST contain only the HTTP version number (e.g.,
        // "1.1", "1.0").
        $message = new Message("1.0");

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // new protocol version.
        $this->assertNotSame($message, $message->withProtocolVersion("1.0"));

        $this->assertEquals("1.1", $message->withProtocolVersion("1.1")->getProtocolVersion());
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
        $message = new Message("1.0");
        $this->assertEquals([], $message->getHeaders());

        $message = new Message("1.0", [
            'Test' => ['Hello', 'World!']
        ]);
        $this->assertEquals(['Test' => ['Hello', 'World!']], $message->getHeaders());
    }

    public function testHasHeader()
    {
        // Checks if a header exists by the given case-insensitive name.
        $message = new Message("1.0", [
            'Test' => ['Hello', 'World!']
        ]);
        $this->assertTrue($message->hasHeader('test'));
        $this->assertTrue($message->hasHeader('teST'));

        $this->assertFalse($message->hasHeader('unknown'));
    }

    public function testGetHeader()
    {
        // This method returns an array of all the header values of the given
        // case-insensitive header name.
        $message = new Message("1.0", [
            'Test' => ['Hello', 'World!']
        ]);
        $this->assertEquals(['Hello', 'World!'], $message->getHeader('test'));
        $this->assertEquals(['Hello', 'World!'], $message->getHeader('tESt'));

        // If the header does not appear in the message, this method MUST return an
        // empty array.
        $this->assertEquals([], $message->getHeader('unknown'));
    }

    public function testGetHeaderLine()
    {
        // This method returns all of the header values of the given
        // case-insensitive header name as a string concatenated together using
        // a comma.
        $message = new Message("1.0", [
            'Test' => ['Hello', 'World!']
        ]);
        $this->assertEquals('Hello,World!', $message->getHeaderLine('test'));
        $this->assertEquals('Hello,World!', $message->getHeaderLine('tESt'));

        // NOTE: Not all header values may be appropriately represented using
        // comma concatenation. For such headers, use getHeader() instead
        // and supply your own delimiter when concatenating.

        // If the header does not appear in the message, this method MUST return
        // an empty string.
        $this->assertEquals('', $message->getHeaderLine('unknown'));
    }

    public function testWithHeader()
    {
        // While header names are case-insensitive, the casing of the header will
        // be preserved by this function, and returned from getHeaders().
        $message = new Message("1.0", [
            'Test' => ['Hello', 'World!']
        ]);
        $this->assertNotSame($message, $message->withHeader('test', 'blabla'));

        try {
            $message->withHeader('test', [[], 'un..']);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid header value. It must be a string or array of strings.', $e->getMessage());
        }
        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // new and/or updated header and value.

        $this->assertEquals(['blabla'], $message->withHeader('other', 'blabla')->getHeader('other'));
        $this->assertEquals(['blabla', 'what'], $message->withHeader('other', ['blabla', 'what'])->getHeader('other'));

        $this->assertEquals(['Hello', 'World!'], $message->withHeader('other', 'blabla')->getHeader('test'));

        $this->assertEquals(['blabla'], $message->withHeader('test', 'blabla')->getHeader('test'));
        $this->assertEquals(['blabla', 'what'], $message->withHeader('test', ['blabla', 'what'])->getHeader('test'));
    }

    public function testWithAddedHeader()
    {
        // Existing values for the specified header will be maintained. The new
        // value(s) will be appended to the existing list. If the header did not
        // exist previously, it will be added.
        $message = new Message("1.0", [
            'Test' => ['Hello', 'World!']
        ]);
        $this->assertNotSame($message, $message->withAddedHeader('test', 'blabla'));

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that has the
        // new header and/or value.
        $this->assertEquals(
            ['Hello', 'World!', 'blabla'],
            $message->withAddedHeader('test', 'blabla')->getHeader('test')
        );

        $this->assertEquals([
            'Test' => ['Hello', 'World!'],
            'other' => ['blabla', 'what']
        ], $message->withAddedHeader('other', ['blabla', 'what'])->getHeaders());
    }

    public function testWithoutHeader()
    {
        // Header resolution MUST be done without case-sensitivity.
        $message = new Message("1.0", [
            'Foo' => ['Hello', 'World!'],
            'bAr' => ['lerem', 'ipsum'],
        ]);
        $this->assertNotSame($message, $message->withoutHeader('foo'));

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return an instance that removes
        // the named header.
        $this->assertEquals([
            'Foo' => ['Hello', 'World!'],
            'bAr' => ['lerem', 'ipsum'],
        ], $message->withoutHeader('other')->getHeaders());

        $this->assertEquals([
            'bAr' => ['lerem', 'ipsum'],
        ], $message->withoutHeader('foo')->getHeaders());

        $this->assertEquals([
            'Foo' => ['Hello', 'World!'],
        ], $message->withoutHeader('baR')->getHeaders());
    }

    public function testGetBody()
    {
        $mockBody = Mockery::mock(StreamInterface::class);

        // Gets the body of the message.
        $message = new Message('1.0', [], $mockBody);
        $this->assertSame($mockBody, $message->getBody());

        $message = new Message('1.0', []);
        $this->assertNull($message->getBody()); // return null
    }

    public function testWithBody()
    {
        $mockBody = Mockery::mock(StreamInterface::class);
        // The body MUST be a StreamInterface object.
        $message = new Message('1.0');
        $this->assertNotSame($message, $message->withBody($mockBody));

        // This method MUST be implemented in such a way as to retain the
        // immutability of the message, and MUST return a new instance that has the
        // new body stream.
        $this->assertSame($mockBody, $message->withBody($mockBody)->getBody());
    }
}
