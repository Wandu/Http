<?php
namespace Wandu\Session;

use InvalidArgumentException;

class Storage
{
    /** @var \Wandu\Session\DataSetInterface */
    protected $dataSet;

    /**
     * @param \Wandu\Session\DataSetInterface $dataSet
     */
    public function __construct(DataSetInterface $dataSet)
    {
        $this->dataSet = $dataSet;
    }



    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException("parameter '{$name}' must be string.");
        }
        return $this->dataSet[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function set($name, $value)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException("parameter '{$name}' must be string.");
        }
        $this->dataSet[$name] = $value;
        return $this;
    }
}
