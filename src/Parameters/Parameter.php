<?php
namespace Wandu\Http\Parameters;

use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Http\Contracts\ParsedBodyInterface;
use Wandu\Http\Contracts\QueryParamsInterface;
use Wandu\Http\Support\Caster;

class Parameter implements QueryParamsInterface, ParsedBodyInterface
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
    public function toArray(array $casts = [])
    {
        $arrayToReturn = $this->params;
        foreach ($casts as $name => $cast) {
            $arrayToReturn[$name] = (new Caster($arrayToReturn[$name]))->cast($cast);
        }
        if (isset($this->fallback)) {
            return $arrayToReturn + $this->fallback->toArray($casts);
        }
        return $arrayToReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null, $cast = null)
    {
        if (array_key_exists($key, $this->params)) {
            $value = $this->params[$key];
        } elseif (isset($this->fallback)) {
            $value = $this->fallback->get($key, $default, $cast);
        } else {
            $value = $default;
        }
        return (new Caster($value))->cast($cast);
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
    public function setData(array $dataSet)
    {
        // TODO: Implement setData() method.
    }
}
