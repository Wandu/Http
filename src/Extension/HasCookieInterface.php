<?php
namespace Wandu\Http\Extension;

use Wandu\Http\Contracts\CookieJarInterface;

interface HasCookieInterface
{
    /**
     * @param \Wandu\Http\Contracts\CookieJarInterface $session
     * @return self
     */
    public function withCookieJar(CookieJarInterface $session);

    /**
     * @return \Wandu\Http\Contracts\CookieJarInterface
     */
    public function getCookieJar();
}
