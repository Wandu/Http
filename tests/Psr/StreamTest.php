<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Mockery;
use RuntimeException;

class StreamTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        new Stream();
        new Stream('php://input');

        try {
            new Stream('unknown');
        } catch (InvalidArgumentException $e) {
            $this->assertequals(
                'Invalid stream "unknown". It must be a valid path with valid permissions.',
                $e->getMessage()
            );
        }
    }

    public function testWrite()
    {
        $stream = new Stream('php://memory', "w+");
        $stream->write("what the?");
        $this->assertEquals('what the?', $stream->__toString());
    }

    public function testMetadata()
    {
        $stream = new Stream('php://memory', "w+");

        $this->assertTrue(is_array($stream->getMetadata()));

        $this->assertEquals(1, $stream->getMetadata('seekable'));
        $this->assertNull($stream->getMetadata('unknown.........'));
    }

    public function testIsWritableAndReadable()
    {
        $fileName = tempnam(__DIR__, '_none_');

        $stream = new Stream($fileName, "r");

        $this->assertFalse($stream->isWritable());
        $this->assertTrue($stream->isReadable());
        try {
            $stream->write('...');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('Stream is not writable.', $e->getMessage());
        }

        $stream = new Stream($fileName, "w");

        $this->assertTrue($stream->isWritable());
        $this->assertFalse($stream->isReadable());
        try {
            $stream->read(1);
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('Stream is not readable.', $e->getMessage());
        }

        $stream = new Stream($fileName, "r+");

        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());

        $stream = new Stream($fileName, "w+");

        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());

        @unlink($fileName);
    }

    public function testReadAndWrite()
    {

        $stream = new Stream('php://memory', 'w+');

        $this->assertEquals(0, $stream->getSize());

        $stream->write("Hello World, And All Developers!");

        $this->assertEquals(32, $stream->getSize()); // size

        $this->assertEquals(32, $stream->tell()); // pointer

        $stream->rewind();

        $this->assertEquals(0, $stream->tell());

        $this->assertFalse($stream->eof());


        $this->assertEquals("Hell", $stream->read(4));
        $this->assertEquals("o World, ", $stream->read(9));
        $this->assertEquals("And All Developers!", $stream->getContents());

        $this->assertTrue($stream->eof());
    }

    public function testCloseAndException()
    {
        $stream = new Stream('php://memory', 'w+');

        $stream->close();
        $stream->close();

        $this->assertFalse($stream->isWritable());
        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->isSeekable());
        $this->assertSame('', $stream->__toString());
        $this->assertNull($stream->getSize());
        $this->assertTrue($stream->eof());
        try {
            $stream->write('...?');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('No resource available.', $e->getMessage());
        }
    }
}
