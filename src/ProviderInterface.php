<?php
namespace Wandu\Session;

interface ProviderInterface
{
    /**
     * @param string $sessionId
     * @return SessionInterface
     */
    public function getSession($sessionId);
}
