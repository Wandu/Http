<?php
namespace Wandu\Http\Extension;

use Wandu\Http\Contracts\SessionInterface;

interface HasSessionInterface
{
    /**
     * @param \Wandu\Http\Contracts\SessionInterface $session
     * @return self
     */
    public function withSession(SessionInterface $session);

    /**
     * @return \Wandu\Http\Contracts\SessionInterface
     */
    public function getSession();
}
