<?php
namespace Wandu\Http\Parameters;

use InvalidArgumentException;
use SessionHandlerInterface;
use Wandu\Http\Contracts\CookieJarInterface;
use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Session\Configuration;

class Session extends Parameter implements SessionInterface  
{
    /** @var \SessionHandlerInterface */
    protected $handler;
    
    /** @var \Wandu\Http\Session\Configuration */
    protected $config;

    /** @var string */
    protected $id;

    public function __construct(
        CookieJarInterface $cookieJar,
        SessionHandlerInterface $handler,
        Configuration $config = null,
        ParameterInterface $fallback = null
    ) {
        $this->handler = $handler;
        $this->config = $config ?: new Configuration();

        $sessionName = $this->config->getName();

        $this->id = $cookieJar->has($sessionName)
            ? $cookieJar->get($sessionName)
            : $this->config->getUniqueId();

        parent::__construct(@unserialize($handler->read($this->id)), $fallback);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $arrayToReturn = parent::toArray();
        if (isset($arrayToReturn['__flash__'])) {
            $arrayToReturn = $arrayToReturn + $arrayToReturn['__flash__'];
            unset($arrayToReturn['__flash__'], $this->params['__flash__']);
        }
        return $arrayToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null, $isStrict = false)
    {
        $this->validNameArgument($name);
        if (isset($this->params['__flash__'][$name]) && ($isStrict || !$isStrict && $this->params['__flash__'][$name])) {
            $resultToReturn = $this->params['__flash__'][$name];
            unset($this->params['__flash__'][$name]);
            return $resultToReturn;
        }
        return parent::get($name, $default, $isStrict);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $this->validNameArgument($name);
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flash($name, $value)
    {
        unset($this->params[$name]);
        if (!isset($this->params['__flash__'])) {
            $this->params['__flash__'] = [];
        }
        $this->params['__flash__'][$name] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        $this->validNameArgument($name);
        return parent::has($name) || // or, __flash__ exists check.
        (isset($this->params['__flash__']) && array_key_exists($name, $this->params['__flash__']));
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $this->validNameArgument($name);
        unset($this->params[$name]);
        return $this;
    }

    /**
     * @param mixed $name
     */
    protected function validNameArgument($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException(sprintf('The session name must be string; "%s"', $name));
        }
        if ($name === '') {
            throw new InvalidArgumentException('The session name cannot be empty.');
        }
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

    /**
     * @param \Wandu\Http\Contracts\CookieJarInterface $cookieJar
     */
    public function applyToCookieJar(CookieJarInterface $cookieJar)
    {
        $sessionName = $this->config->getName();

        // save to handler
        $this->handler->write($this->id, serialize($this->params));

        // apply to cookie-jar
        $cookieJar->set(
            $sessionName,
            $this->id,
            (new \DateTime())->setTimestamp(time() + $this->config->getTimeout())
        );

        // garbage collection
        $pick = rand(1, max(1, $this->config->getGcFrequency()));
        if ($pick === 1) {
            $this->handler->gc($this->config->getTimeout());
        }
    }
}
