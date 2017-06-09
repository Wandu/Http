<?php
namespace Wandu\Http\Parameters;

use ArrayIterator;
use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Http\Exception\CannotCallMethodException;

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
    public function getMany(array $keyOrDefaults = [], $isStrict = false)
    {
        $dataToReturn = [];
        foreach ($keyOrDefaults as $key => $value) {
            if (is_integer($key)) {
                $dataToReturn[$value] = $this->get($value, null, $isStrict);
            } else {
                $dataToReturn[$key] = $this->get($key, $value, $isStrict);
            }
        }
        return $dataToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null, $isStrict = false)
    {
        if ($key === '') {
            return $this->params;
        }
        $keys = explode('.', $key);
        $dataToReturn = $this->params;
        while (count($keys)) {
            $key = array_shift($keys);
            if (!is_array($dataToReturn) || !array_key_exists($key, $dataToReturn)) {
                if (isset($this->fallback)) {
                    return $this->fallback->get($key, $default);
                }
                return $default;
            }
            $dataToReturn = $dataToReturn[$key];
        }
        if (isset($dataToReturn) && ($isStrict || !$isStrict && $dataToReturn)) {
            return $dataToReturn;
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
        if ($key === '') {
            return true;
        }
        $keys = explode('.', $key);
        $dataToReturn = $this->params;
        while (count($keys)) {
            $key = array_shift($keys);
            if (!is_array($dataToReturn) || !isset($dataToReturn[$key])) {
                if (isset($this->fallback) && $this->fallback->has($key)) {
                    return true;
                }
                return false;
            }
            $dataToReturn = $dataToReturn[$key];
        }
        return true;
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

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }
}
