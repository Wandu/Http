<?php
namespace Wandu\Http\Parameters;

use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Http\Contracts\ParsedBodyInterface;
use Wandu\Http\Contracts\QueryParamsInterface;

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
            $arrayToReturn[$name] = $this->applyCasting($arrayToReturn[$name], $cast);
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
        return $this->applyCasting($value, $cast);
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
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function applyCasting($value, $type = null)
    {
        if (!isset($type)) {
            return $value;
        }
        if (($p = strpos($type, '[]')) !== false || $type === 'array') {
            if (!is_array($value)) {
                if (strpos($value, ',') !== false) {
                    $value = explode(',', $value);
                } else {
                    $value = [$value];
                }
            }
            if ($type === 'array') {
                return $value;
            }
            $typeInArray = substr($type, 0, $p);
            return array_map(function ($item) use ($typeInArray) {
                return $this->applyCasting($item, $typeInArray);
            }, $value);
        }
        switch ($type) {
            case 'string':
                if (is_array($value)) {
                    return implode(',', $value);
                }
                break;
            case "number":
                $type = 'float';
                break;
            case "bool":
            case "boolean":
                if ($value === 'false') {
                    $value = false;
                }
                break;
        }
        settype($value, $type);
        return $value;
    }
}
