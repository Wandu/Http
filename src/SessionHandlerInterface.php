<?php
namespace Wandu\Session;

interface SessionHandlerInterface
{
    /**
     * @param string $sessionId
     * @return array
     */
    public function read($sessionId);

    /**
     * @param string $sessionId
     * @param array $data
     */
    public function write($sessionId, array $data);

    /**
     * @param string $sessionId
     */
    public function destroy($sessionId);
}
