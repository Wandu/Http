<?php
namespace Wandu\Http\Psr\Stream;

use Mockery;
use PHPUnit_Framework_TestCase;
use RuntimeException;

class GeneratorStreamTest extends PHPUnit_Framework_TestCase
{
    /** @var \Wandu\Http\Psr\Stream\GeneratorStream */
    protected $stream;

    public function setUp()
    {
        $this->stream = new GeneratorStream(function () {
            for ($i = 0; $i < 10; $i++) {
                yield sprintf("%02d ", $i);
            }
        });
    }

    public function testWrite()
    {
        $this->assertFalse($this->stream->isWritable());
        try {
            $this->stream->write("some...");
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('GeneratorStream cannot write.', $e->getMessage());
        }
    }

    public function testSeek()
    {
        $this->assertFalse($this->stream->isSeekable());
        try {
            $this->stream->seek(0);
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('GeneratorStream cannot seek.', $e->getMessage());
        }
    }

    public function testRead()
    {
        $this->assertFalse($this->stream->isReadable());
        try {
            $this->stream->read(10);
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('GeneratorStream cannot read.', $e->getMessage());
        }
    }

    public function testRewindAndGetContents()
    {
        $this->assertFalse($this->stream->eof());
        $this->assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->getContents()
        );
        $this->assertTrue($this->stream->eof());
        $this->assertEquals(
            '',
            $this->stream->getContents()
        );

        // rewind
        $this->stream->rewind();

        $this->assertFalse($this->stream->eof());
        $this->assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->getContents()
        );
        $this->assertTrue($this->stream->eof());
        $this->assertEquals(
            '',
            $this->stream->getContents()
        );
    }

    public function testToString()
    {
        $this->assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->__toString()
        );
        $this->assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->__toString()
        );
        $this->assertEquals(
            '00 01 02 03 04 05 06 07 08 09 ',
            $this->stream->__toString()
        );
    }
}
