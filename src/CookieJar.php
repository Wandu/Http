<?php
namespace Wandu\Cookie;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CookieJar
{
    /** @var \Wandu\Cookie\Cookie[] */
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