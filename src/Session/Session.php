<?php
namespace Wandu\Http\Session;

use InvalidArgumentException;
use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Http\Contracts\SessionInterface;
use Wandu\Http\Parameters\Parameter;
use Wandu\Http\Support\Caster;

class Session extends Parameter implements SessionInterface
{
    /** @var string */
    protected $id;

    /**
     * @param array $id
     * @param array $dataSet
     * @param \Wandu\Http\Contracts\ParameterInterface|null $fallback
     */
    public function __construct($id, array $dataSet = [], ParameterInterface $fallback = null)
    {
        $this->id = $id;
        parent::__construct($dataSet, $fallback);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null, $cast = null)
    {
        $this->validNameArgument($name);
        if (isset($this->params['_flash'][$name])) {
            $resultToReturn = $this->params['_flash'][$name];
            unset($this->params['_flash'][$name]);
            return (new Caster($resultToReturn))->cast($cast);
        }
        return parent::get($name, $default, $cast);
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
        if (!isset($this->params['_flash'])) {
            $this->params['_flash'] = [];
        }
        $this->params['_flash'][$name] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        $this->validNameArgument($name);
        return parent::has($name) || // or, _flash exists check.
            (isset($this->params['_flash']) && array_key_exists($name, $this->params['_flash']));
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
}
