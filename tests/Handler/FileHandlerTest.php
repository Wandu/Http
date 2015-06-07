<?php
namespace Wandu\Session\Handler;

use PHPUnit_Framework_TestCase;
use Mockery;

class FileHandlerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!is_dir(__DIR__ . '/sessions')) {
            mkdir(__DIR__ . '/sessions');
        }
    }

    public function tearDown()
    {
        $this->deleteAll(__DIR__ . '/sessions');
        Mockery::close();
    }

    protected function deleteAll($directory)
    {
        $files = array_diff(scandir($directory), ['.','..']);
        foreach ($files as $file) {
            is_dir("{$directory}/{$file}") ? $this->deleteAll("{$directory}/{$file}") : unlink("{$directory}/{$file}");
        }
        return rmdir($directory);
    }

    public function testConstruct()
    {
        $sessionId = sha1(uniqid());

        $file = new FileHandler(__DIR__ . '/sessions');
        $session = $file->read($sessionId);

        $this->assertEquals([], $session);

        $session['hello'] = 'world';
        $session['what'] = "um..";

        // clear one cycle!
        $file->write($sessionId, $session);


        $this->assertEquals([
            'hello' => 'world',
            'what' => 'um..'
        ], $file->read($sessionId));

        // destroy
        $file->destroy($sessionId);

        $this->assertEquals([], $file->read($sessionId));
    }
}
