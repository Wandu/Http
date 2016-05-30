<?php
namespace Wandu\Http\Session\Handler;

use SessionHandlerInterface;

class GlobalHandler implements SessionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->bootSession();
        session_destroy();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        $this->bootSession();
        if (count($_SESSION)) {
            return serialize($_SESSION);
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        $this->bootSession();
        $dataSet = unserialize($sessionData);
        foreach ($dataSet as $key => $value) {
            $_SESSION[$key] = $value;
        }
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
    public function gc($maxLifeTime)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }
    
    protected function bootSession()
    {
        if (session_status() == \PHP_SESSION_NONE) {
            session_start();
        }
    }
}
