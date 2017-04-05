<?php
namespace Wandu\Http\Parameters;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Http\Cookie\Cookie;

class CookieJar extends Parameter implements CookieJarInterface
{
    /** @var \Wandu\Http\Cookie\Cookie[] */
    protected $setCookies = [];

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Wandu\Http\Contracts\ParameterInterface $fallback
     */
    public function __construct(ServerRequestInterface $request, ParameterInterface $fallback = null)
    {
        parent::__construct($request->getCookieParams(), $fallback);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $resultToReturn = parent::toArray();
        foreach ($this->setCookies as $name => $setCookie) {
            if ($value = $setCookie->getValue()) {
                $resultToReturn[$name] = $value;
            } else {
                unset($resultToReturn[$name]);
            }
        }
        return $resultToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null, $isStrict = false)
    {
        if (isset($this->setCookies[$name]) && $this->setCookies[$name]->getValue()) {
            return $this->setCookies[$name]->getValue();
        }
        return parent::get($name, $default, $isStrict);
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
        if (isset($this->setCookies[$name]) && $this->setCookies[$name]->getValue()) {
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

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function applyToResponse(ResponseInterface $response)
    {
        foreach ($this->setCookies as $cookie) {
            $response = $response->withAddedHeader('Set-Cookie', $cookie->__toString());
        }
        return $response;
    }
}
