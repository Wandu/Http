<?php
namespace Wandu\Http\Session\Adapter;

use PHPUnit_Framework_TestCase;

class FileAdapterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!is_dir(__DIR__ . '/sessions')) {
            mkdir(__DIR__ . '/sessions');
        }
        $this->adapter = new FileAdapter(__DIR__ . '/sessions');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->deleteAll(__DIR__ . '/sessions');
    }

    protected function deleteAll($directory)
    {
        $files = array_diff(scandir($directory), ['.','..']);
        foreach ($files as $file) {
            is_dir("{$directory}/{$file}") ? $this->deleteAll("{$directory}/{$file}") : unlink("{$directory}/{$file}");
        }
        return rmdir($directory);
    }

    public function testEmptySession()
    {
        if (!isset($this->adapter)) {
            $this->markTestSkipped('there is no adapter! :-)');
        }
        $session = $this->adapter->read(sha1(uniqid()));

        $this->assertEquals([], $session);
    }

    public function testWriteSession()
    {
        if (!isset($this->adapter)) {
            $this->markTestSkipped('there is no adapter! :-)');
        }
        $sessionId = sha1(uniqid());

        // write
        $this->adapter->write($sessionId, [
            'hello' => 'world',
            'what' => 'um..'
        ]);

        // then data
        $this->assertEquals([
            'hello' => 'world',
            'what' => 'um..'
        ], $this->adapter->read($sessionId));

        // destroy
        $this->adapter->destroy($sessionId);

        // then blank
        $this->assertEquals([], $this->adapter->read($sessionId));
    }
}
