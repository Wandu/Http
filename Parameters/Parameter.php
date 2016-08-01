<?php
namespace Wandu\Http\Parameters;

use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Support\Exception\CannotCallMethodException;

class Parameter implements ParameterInterface
{
    /** @var array */
    protected $params;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $fallback;

    /**
     * @param array $params
     * @param \Wandu\Http\Contracts\ParameterInterface $fallback
     */
    public function __construct(array $params = [], ParameterInterface $fallback = null)
    {
        $this->params = $params;
        $this->fallback = $fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function setFallback(ParameterInterface $fallback)
    {
        $oldFallback = $this->fallback;
        $this->fallback = $fallback;
        return $oldFallback;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $arrayToReturn = $this->params;
        if (isset($this->fallback)) {
            return $arrayToReturn + $this->fallback->toArray();
        }
        return $arrayToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function getMany(array $keyOrDefaults = [])
    {
        $dataToReturn = [];
        foreach ($keyOrDefaults as $key => $value) {
            if (is_integer($key)) {
                if ($this->has($value)) {
                    $dataToReturn[$value] = $this->get($value);
                }
            } else {
                $dataToReturn[$key] = $this->get($key, $value);
            }
        }
        return $dataToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        if (isset($this->fallback)) {
            return $this->fallback->get($key, $default);
        }
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        if (array_key_exists($key, $this->params)) {
            return true;
        }
        if (isset($this->fallback) && $this->fallback->has($key)) {
            return true;
        }
        return false;
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
        if (strpos($offset, '||') !== false) {
            list($offset, $default) = explode('||', $offset);
            return $this->get($offset, $default);
        }
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new CannotCallMethodException(__FUNCTION__, __CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new CannotCallMethodException(__FUNCTION__, __CLASS__);
    }
}
