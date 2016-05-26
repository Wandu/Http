<?php
namespace Wandu\Http\Session\Handler;

use DirectoryIterator;
use SessionHandlerInterface;

class FileHandler implements SessionHandlerInterface
{
    /** @var string */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        unlink($this->path . '/' . $sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        $path = $this->path . '/' . $sessionId;
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        $path = $this->path . '/' . $sessionId;
        file_put_contents($path, $sessionData);
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxLifeTime)
    {
        $iter = new DirectoryIterator($this->path);
        $now = time();
        $result = true;
        foreach ($iter as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            if ($fileInfo->getMTime() + $maxLifeTime > $now) continue;
            $result = $result && @unlink($fileInfo->getRealPath());
        }
        return $result;
    }
}
