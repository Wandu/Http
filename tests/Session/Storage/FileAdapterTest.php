<?php
namespace Wandu\Http\Session\Storage;

class FileAdapterTest extends AdapterBaseTestCase
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
}
