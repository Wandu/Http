<?php
namespace Wandu\Http\Session;

use Wandu\Http\Contracts\SessionInterface;

interface SessionAdapterInterface
{
    /**
     * @param string $sessionId
     * @return \Wandu\Http\Contracts\SessionInterface
     */
    public function read($sessionId);

    /**
     * @param $sessionId
     * @param \Wandu\Http\Contracts\SessionInterface $session
     */
    public function write($sessionId, SessionInterface $session);

    /**
     * @param string $sessionId
     */
    public function destroy($sessionId);
}
