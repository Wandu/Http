<?php
namespace Wandu\Http\File;

use Mockery;
use PHPUnit_Framework_TestCase;
use InvalidArgumentexception;
use Wandu\Http\Psr\UploadedFile;

class UploaderTest extends PHPUnit_Framework_TestCase
{
    public function testSuccessToConstruct()
    {
        new Uploader(__DIR__);

        // not exists directory
        new Uploader(__DIR__ . '/notexists', true);
        rmdir(__DIR__ . '/notexists');
    }

    public function testFailToConstruct()
    {
        // file
        try {
            new Uploader(__FILE__);
            $this->fail();
        } catch (InvalidArgumentexception $e) {
        }

        // not exists directory
        try {
            new Uploader(__DIR__ . '/notexists');
            $this->fail();
        } catch (InvalidArgumentexception $e) {
        }

        // do not permit directory
        mkdir(__DIR__ . '/cannot', 0);
        try {
            new Uploader(__DIR__ . '/cannot');
            $this->fail();
        } catch (InvalidArgumentexception $e) {
        }
        rmdir(__DIR__ . '/cannot');
    }

    public function testUploadFile()
    {
        $uploader = new Uploader(__DIR__);

        // has error return null (and not any action)
        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getError')->once()->andReturn(UploadedFile::ERR_NO_FILE);
        $file->shouldReceive('moveTo')->never();

        $this->assertNull($uploader->uploadFile($file));

        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getError')->once()->andReturn(UploadedFile::OK);
        $file->shouldReceive('getClientFilename')->once()->andReturn('helloworld.png');
        $file->shouldReceive('moveTo')->once();

        $this->assertRegExp('/\d{6}\\/[0-9a-f]{40}\\.png/', $file = $uploader->uploadFile($file));
        $this->asserttrue(is_dir(__DIR__ . '/' .pathinfo($file)['dirname'])); // auto dir created

        @rmdir(__DIR__ . '/' .date('ymd'));
    }

    public function testUploadFiles()
    {
        $erroredFile = Mockery::mock(UploadedFile::class);
        $erroredFile->shouldReceive('getError')->andReturn(UploadedFile::ERR_NO_FILE);

        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getError')->andReturn(UploadedFile::OK);
        $file->shouldReceive('getClientFilename')->andReturn('helloworld.png');
        $file->shouldReceive('moveTo');

        $uploader = new Uploader(__DIR__);
        $result = $uploader->uploadFiles([
            'foo' => $file,
            'bar' => $file,
            'baz' => [
                $file, $erroredFile, $file, $file,
            ],
            'qux' => $erroredFile,
        ]);

        $this->assertTrue(is_string($result['foo']));
        $this->assertTrue(is_string($result['bar']));
        $this->assertTrue(is_string($result['baz'][0]));
        $this->assertFalse(array_key_exists(1, $result['baz']));
        $this->assertTrue(is_string($result['baz'][2]));
        $this->assertTrue(is_string($result['baz'][3]));

        $this->assertFalse(array_key_exists('qux', $result));

        @rmdir(__DIR__ . '/' .date('ymd'));
    }
}
