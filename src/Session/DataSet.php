<?php
namespace Wandu\Http\Session;

class DataSet implements DataSetInterface
{
    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $dataSet)
    {
        $instance = new static();
        $instance->dataSet = $dataSet;
        return $instance;
    }

    /** @var array */
    private $dataSet = [];

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
    public function offsetExists($offset)
    {
        return isset($this->dataSet[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->dataSet[$offset]) ? $this->dataSet[$offset] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->dataSet[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->dataSet[$offset]);
    }
}
