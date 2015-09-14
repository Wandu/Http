<?php
namespace Wandu\Http\Cookie;

use DateTime;
use Wandu\Http\Contracts\CookieJarInterface;

class CookieJar implements CookieJarInterface
{
    /** @var array */
    protected $cookies;

    /** @var \Wandu\Http\Cookie\Cookie[] */
    protected $setCookies;

    /**
     * @param array $cookieParams
     */
    public function __construct(array $cookieParams = [])
    {
        $this->cookies = $cookieParams;
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
}
