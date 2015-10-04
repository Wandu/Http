<?php
namespace Wandu\Http\Psr;

use PHPUnit_Framework_TestCase;
use Mockery;
use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Wandu\Http\Psr\UploadedFile;

class UploadedFileTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(UploadedFileInterface::class, new UploadedFile);

        $file = new UploadedFile('/tmp/hello', 3030, 0, 'hello.txt', 'text/plain');
        $this->assertSame(3030, $file->getSize());
        $this->assertSame(0, $file->getError());
        $this->assertSame('hello.txt', $file->getClientFilename());
        $this->assertSame('text/plain', $file->getClientMediaType());
    }

    public function testGetStream()
    {
        $fileName = tempnam(__DIR__, 'uf_');

        // This method MUST return a StreamInterface instance, representing the
        // uploaded file. The purpose of this method is to allow utilizing native PHP
        // stream functionality to manipulate the file upload, such as
        // stream_copy_to_stream() (though the result will need to be decorated in a
        // native PHP stream wrapper to work with such functions).
        $file = new UploadedFile($fileName);

        $this->assertInstanceOf(StreamInterface::class, $file->getStream());

        // If the moveTo() method has been called previously, this method MUST raise
        // an exception.
        $file->moveTo($fileName.'_moved');

        try {
            $file->getStream();
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('Cannot retrieve stream after it has already been moved.', $e->getMessage());
        }

        @unlink($fileName.'_moved');
    }

    public function testMoveTo()
    {
        $fileName = tempnam(__DIR__, 'uf_');
        file_put_contents($fileName, 'hello world...');

        // Move the uploaded file to a new location.

        // Use this method as an alternative to move_uploaded_file(). This method is
        // guaranteed to work in both SAPI and non-SAPI environments.
        // Implementations must determine which environment they are in, and use the
        // appropriate method (move_uploaded_file(), rename(), or a stream
        // operation) to perform the operation.

        // $targetPath may be an absolute path, or a relative path. If it is a
        // relative path, resolution should be the same as used by PHP's rename()
        // function.

        $file = new UploadedFile($fileName);
        $file->moveTo($fileName .'_moved');

        // The original file or stream MUST be removed on completion.
        $this->assertFileNotExists($fileName); // old file check
        $this->assertFileExists($fileName .'_moved');
        $this->assertEquals('hello world...', file_get_contents($fileName .'_moved'));

        // If this method is called more than once, any subsequent calls MUST raise
        // an exception.
        try {
            $file->moveTo($fileName . '_moved');
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertEquals('Cannot move the file. Already moved!', $e->getMessage());
        }

        // When used in an SAPI environment where $_FILES is populated, when writing
        // files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
        // used to ensure permissions and upload status are verified correctly.

        // If you wish to move to a stream, use getStream(), as SAPI operations
        // cannot guarantee writing to stream destinations.

        // mores..
        try {
            $file->moveTo([]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid path provided for move operation. It must be a string.', $e->getMessage());
        }

        @unlink($fileName .'_moved');
    }
}
