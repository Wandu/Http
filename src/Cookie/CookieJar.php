<?php
namespace Wandu\Http\Cookie;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Contracts\CookieJarInterface;

class CookieJar implements CookieJarInterface
{
    /** @var \Wandu\Http\Cookie\Cookie[] */
    protected $setCookies;

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request = null)
    {
        $this->cookies = isset($request) ? $request->getCookieParams() : [];
        $this->setCookies = [];
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function get($name)
    {
        return isset($this->cookies[$name]) ? $this->cookies[$name] : null;
    }

    /**
     * @param string $name
     * @param string $value
     * @param \DateTime $expire
     * @return self
     */
    public function set($name, $value, DateTime $expire = null)
    {
        $this->setCookies[$name] = new Cookie($name, $value, isset($expire) ? $expire->format('U') : null);
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->cookies[$name]);
    }

    /**
     * @param string $name
     * @return self
     */
    public function remove($name)
    {
        $this->setCookies[$name] = new Cookie($name);
        return $this;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function withSetCookieHeader(ResponseInterface $response)
    {
        foreach ($this->setCookies as $setCookie) {
            $response = $response->withAddedHeader('Set-Cookie', $setCookie->__toString());
        }
        return $response;
    }
}