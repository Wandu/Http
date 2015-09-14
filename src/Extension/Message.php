<?php
namespace Wandu\Http\Extension;

use Psr\Http\Message\StreamInterface;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\Extension\MessageInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Cookie\CookieJar;
use Wandu\Http\Session\Session;
use Wandu\Http\Traits\MessageTrait;

class Message implements MessageInterface
{
    use MessageTrait;

    /** @var \Wandu\Http\Contracts\CookieJarInterface */
    protected $cookieJar;

    /** @var \Wandu\Http\Contracts\SessionInterface */
    protected $session;

    /**
     * {@inheritdoc}
     */
    public function withCookieJar(CookieJarInterface $cookieJar)
    {
        $new = clone $this;
        $new->cookieJar = $cookieJar;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieJar()
    {
        return $this->cookieJar ?: new CookieJar();
    }

    /**
     * {@inheritdoc}
     */
    public function withSession(SessionInterface $session)
    {
        $new = clone $this;
        $new->session = $session;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getSession()
    {
        return $this->session ?: new Session();
    }
}
