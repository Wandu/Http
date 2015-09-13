<?php
namespace Wandu\Http\Session;

use InvalidArgumentException;
use Wandu\Http\Contracts\SessionInterface;

class Session implements SessionInterface
{
    /** @var array */
    protected $dataSet;

    /**
     * @param array $dataSet
     */
    public function __construct(array $dataSet = [])
    {
        $this->dataSet = $dataSet;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->dataSet;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $this->validNameArgument($name);
        return $this->dataSet[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $this->validNameArgument($name);
        $this->dataSet[$name] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        $this->validNameArgument($name);
        return isset($this->dataSet[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $this->validNameArgument($name);
        unset($this->dataSet[$name]);
        return $this;
    }

    /**
     * @param mixed $name
     */
    protected function validNameArgument($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException("parameter '{$name}' must be string.");
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
}
