<?php
namespace Wandu\Http\Parameters;

class Parameter implements QueryParamsInterface, ParsedBodyInterface
{
    /** @var array */
    protected $params;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @param array $option
     * @return mixed
     */
    public function get($key, $default = null, array $option = [])
    {
        $value = isset($this->params[$key]) ? $this->params[$key] : $default;
        if (isset($option['cast'])) {
            switch ($option['cast']) {
                case "integer":
                case "int":
                    $value = (int) $value;
                    break;
                case "integer[]":
                case "int[]":
                    if (!is_array($value)) {
                        $value = [$value];
                    }
                    $value = array_map(function ($item) {
                        return (int) $item;
                    }, $value);
                    break;
            }
        }
        return $value;
    }
}
