<?php
namespace Wandu\Session\Provider;

use PHPUnit_Framework_TestCase;
use Mockery;
use Psr\Http\Message\ServerRequestInterface;

class FileTest extends PHPUnit_Framework_TestCase
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

        $file = new FileProvider(__DIR__ . '/sessions');
        $session = $file->getSession($sessionId);

        $session->set('foo', 'hello');
        $session->set('what', ['abc', 'def']);

        unset($session);

        $session = $file->getSession($sessionId);
        $this->assertEquals('hello', $session->get('foo'));
        $this->assertEquals(['abc', 'def'], $session->get('what'));
    }
}
