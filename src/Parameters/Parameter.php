<?php
namespace Wandu\Http\Parameters;

class Parameter implements QueryParamsInterface, ParsedBodyInterface
{
    /** @var array */
    protected $params;

    /** @var \Wandu\Http\Parameters\ParameterInterface */
    protected $fallback;

    /**
     * @param array $params
     * @param \Wandu\Http\Parameters\ParameterInterface $fallback
     */
    public function __construct(array $params = [], ParameterInterface $fallback = null)
    {
        $this->params = $params;
        $this->fallback = $fallback;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @param array $option
     * @return mixed
     */
    public function get($key, $default = null, array $option = [])
    {
        if (array_key_exists($key, $this->params)) {
            $value = $this->params[$key];
        } elseif (isset($this->fallback)) {
            $value = $this->fallback->get($key, $default, $option);
        } else {
            $value = $default;
        }
        if (isset($option['cast'])) {
            $value = $this->applyCasting($value, $option['cast']);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function applyCasting($value, $type)
    {
        if (strpos($type, '[]') !== false || $type === 'array') {
            if (!is_array($value)) {
                if (strpos($value, ',') !== false) {
                    $value = explode(',', $value);
                } else {
                    $value = [$value];
                }
            }
        }
        switch ($type) {
            case 'string':
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                break;
            case "integer":
            case "int":
                $value = (int) $value;
                break;
            case "integer[]":
            case "int[]":
                $value = array_map(function ($item) {
                    return (integer) $item;
                }, $value);
                break;
            case "float":
                $value = (float) $value;
                break;
            case "number":
            case "double":
                $value = (double) $value;
                break;
            case "float[]":
                $value = array_map(function ($item) {
                    return (float) $item;
                }, $value);
                break;
            case "number[]":
            case "double[]":
                $value = array_map(function ($item) {
                    return (double) $item;
                }, $value);
                break;
            case "bool":
            case "boolean":
                $value = (bool) $value;
                break;
            case "bool[]":
            case "boolean[]":
                $value = array_map(function ($item) {
                    return (bool) $item;
                }, $value);
                break;
        }
        return $value;
    }
}
