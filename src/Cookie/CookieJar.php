<?php
namespace Wandu\Http\Cookie;

use ArrayIterator;
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
     * @return array
     */
    public function toArray()
    {
        return $this->setCookies + $this->cookies;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function get($name)
    {
        if (isset($this->setCookies[$name])) {
            return $this->setCookies[$name]->getValue();
        }
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
        $this->setCookies[$name] = new Cookie($name, $value, isset($expire) ? $expire : null);
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->get($name) !== null;
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
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->setCookies);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
