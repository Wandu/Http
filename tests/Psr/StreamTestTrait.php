<?php
namespace Wandu\Http\Psr;

use Mockery;
use RuntimeException;

trait StreamTestTrait
{
    /** @var \Psr\Http\Message\StreamInterface */
    protected $stream;

    public function testWrite()
    {
        $this->stream->write("what the?");
        $this->assertEquals('what the?', $this->stream->__toString());
    }

    public function testGetMetadata()
    {
        $this->assertTrue(is_array($this->stream->getMetadata()));
        $this->assertNull($this->stream->getMetadata('unknown.........'));
    }

    public function testSeek()
    {
        $this->stream->write("Hello World");
        try {
            $this->stream->seek(100);
            $this->fail();
        } catch (RuntimeException $e) {
        }
        $this->stream->seek(6);

        $this->assertEquals('World', $this->stream->getContents());
    }

    public function testReadAndWrite()
    {
        $this->assertEquals(0, $this->stream->getSize());

        $this->stream->write("Hello World, And All Developers!");

        $this->assertEquals(32, $this->stream->getSize()); // size
        $this->assertEquals(32, $this->stream->tell()); // pointer

        $this->stream->rewind();

        $this->assertEquals(0, $this->stream->tell());
        $this->assertFalse($this->stream->eof());


        $this->assertEquals("Hell", $this->stream->read(4));
        $this->assertEquals("o World, ", $this->stream->read(9));
        $this->assertEquals("And All Developers!", $this->stream->getContents());

        $this->assertTrue($this->stream->eof());

        $this->stream->seek(12);
        $this->assertEquals(6, $this->stream->write('Hum...'));

        $this->assertEquals("ll Developers!", $this->stream->getContents());
        $this->assertEquals("Hello World,Hum...ll Developers!", $this->stream->__toString());
    }

    public function testNullToString()
    {
        $this->assertSame('', $this->stream->__toString());
    }
}
