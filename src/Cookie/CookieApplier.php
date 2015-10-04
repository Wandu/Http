<?php
namespace Wandu\Http\Cookie;

use Psr\Http\Message\ResponseInterface;
use Wandu\Http\Contracts\CookieJarInterface;

class CookieApplier
{
    /** @var \Wandu\Http\Contracts\CookieJarInterface */
    protected $cookieJar;

    /**
     * @param \Wandu\Http\Contracts\CookieJarInterface $cookieJar
     */
    public function __construct(CookieJarInterface $cookieJar)
    {
        $this->cookieJar = $cookieJar;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function apply(ResponseInterface $response)
    {
        foreach ($this->cookieJar as $cookie) {
            $response = $response->withAddedHeader('Set-Cookie', $cookie->__toString());
        }
        return $response;
    }
}
