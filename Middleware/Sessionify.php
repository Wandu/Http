<?php
namespace Wandu\Http\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Cookie\CookieJarFactory;
use Wandu\Http\Session\SessionFactory;

class Sessionify
{
    /** @var \Wandu\Http\Cookie\CookieJarFactory */
    protected $cookieJarFactory;

    /** @var \Wandu\Http\Session\SessionFactory */
    protected $sessionFactory;

    /**
     * @param \Wandu\Http\Cookie\CookieJarFactory $cookieJarFactory
     * @param \Wandu\Http\Session\SessionFactory $sessionFactory
     */
    public function __construct(
        CookieJarFactory $cookieJarFactory,
        SessionFactory $sessionFactory
    ) {
        $this->cookieJarFactory = $cookieJarFactory;
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Closure $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        $cookieJar = $this->cookieJarFactory->fromServerRequest($request);
        $session = $this->sessionFactory->fromCookieJar($cookieJar);

        $request = $request
            ->withAttribute('cookie', $cookieJar)
            ->withAttribute('session', $session);

        // run next
        $response = $next($request);

        $this->sessionFactory->toCookieJar($session, $cookieJar);
        return $this->cookieJarFactory->toResponse($cookieJar, $response);
    }
}
