<?php
namespace Wandu\Http\Psr\Stream;

use Mockery;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use Wandu\Http\Psr\StreamTestTrait;

class StringStreamTest extends PHPUnit_Framework_TestCase
{
    use StreamTestTrait;

    public function setUp()
    {
        $this->stream = new StringStream('');
    }

    public function testAllwaysTrueMethods()
    {
        $this->assertTrue($this->stream->isReadable());
        $this->assertTrue($this->stream->isSeekable());
        $this->assertTrue($this->stream->isWritable());
    }

    public function testCannotUseMethods()
    {
        try {
            $this->stream->close();
            $this->fail();
        } catch (RuntimeException $e) {
        }
        try {
            $this->stream->detach();
            $this->fail();
        } catch (RuntimeException $e) {
        }
    }
}
