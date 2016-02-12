<?php
namespace Wandu\Http\Cookie;

use ArrayIterator;
use DateTime;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Http\Parameters\Parameter;
use Wandu\Http\Support\Caster;

class CookieJar extends Parameter implements CookieJarInterface
{
    /** @var \Wandu\Http\Cookie\Cookie[] */
    protected $setCookies;

    /**
     * @param array $cookieParams
     * @param \Wandu\Http\Contracts\ParameterInterface $fallback
     */
    public function __construct(array $cookieParams = [], ParameterInterface $fallback = null)
    {
        parent::__construct($cookieParams, $fallback);
        $this->setCookies = [];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $casts = [])
    {
        $resultToReturn = [];
        foreach ($this->setCookies as $name => $setCookie) {
            $resultToReturn[$name] = $setCookie->getValue();
        }
        return $resultToReturn + parent::toArray($casts);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null, $cast = null)
    {
        if (isset($this->setCookies[$name])) {
            return (new Caster($this->setCookies[$name]->getValue()))->cast($cast);
        }
        return parent::get($name, $default, $cast);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value, DateTime $expire = null)
    {
        $this->setCookies[$name] = new Cookie($name, $value, isset($expire) ? $expire : null);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        if (array_key_exists($name, $this->setCookies) && $this->setCookies[$name]->getValue()) {
            return true;
        }
        return parent::has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $this->setCookies[$name] = new Cookie($name);
        unset($this->params[$name]);
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
