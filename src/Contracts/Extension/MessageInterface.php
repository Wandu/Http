<?php
namespace Wandu\Http\Contracts\Extension;

use Psr\Http\Message\MessageInterface as PsrMessageInterface;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\SessionInterface;

interface MessageInterface extends PsrMessageInterface
{
    /**
     * @param \Wandu\Http\Contracts\CookieJarInterface $cookieJar
     * @return self
     */
    public function withCookieJar(CookieJarInterface $cookieJar);

    /**
     * @return \Wandu\Http\Contracts\CookieJarInterface
     */
    public function getCookieJar();

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
