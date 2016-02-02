<?php
namespace Wandu\Http\Session\Adapter;

use Wandu\Http\Contracts\SessionAdapterInterface;

class GlobalAdapter implements SessionAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        session_start();
        return $_SESSION;
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, array $dataSet)
    {
        foreach ($dataSet as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
}
