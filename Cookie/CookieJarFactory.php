<?php
namespace Wandu\Http\Cookie;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Contracts\CookieJarInterface;

/**
 * @deprecated use \Wandu\Http\Parameters\CookieJar
 */
class CookieJarFactory
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Wandu\Http\Contracts\CookieJarInterface
     */
    public function fromServerRequest(ServerRequestInterface $request)
    {
        return new CookieJar($request->getCookieParams());
    }

    /**
     * @param \Wandu\Http\Contracts\CookieJarInterface $cookieJar
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function toResponse(CookieJarInterface $cookieJar, ResponseInterface $response)
    {
        foreach ($cookieJar as $cookie) {
            $response = $response->withAddedHeader('Set-Cookie', $cookie->__toString());
        }
        return $response;
    }
}
