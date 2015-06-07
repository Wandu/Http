<?php
namespace Wandu\Session\Handler;

use Wandu\Session\SessionHandlerInterface;

class FileHandler implements SessionHandlerInterface
{
    /** @var string */
    private $fileRoot;

    /**
     * @param string $fileRoot
     */
    public function __construct($fileRoot)
    {
        $this->fileRoot = $fileRoot;
    }

    /**
     * @param string $sessionId
     */
    public function destroy($sessionId)
    {
        unlink($this->fileRoot . '/' . $sessionId);
    }

    /**
     * @param string $sessionId
     * @return array
     */
    public function read($sessionId)
    {
        $path = $this->fileRoot . '/' . $sessionId;
        return file_exists($path) ? unserialize(file_get_contents($path)) : [];
    }

    /**
     * @param string $sessionId
     * @param array $data
     */
    public function write($sessionId, array $data)
    {
        $path = $this->fileRoot . '/' . $sessionId;
        file_put_contents(
            $path,
            serialize($data)
        );
    }
}
