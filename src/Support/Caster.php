<?php
namespace Wandu\Http\Support;

class Caster
{
    /** @var mixed */
    protected $value;

    /** @var array */
    protected $boolFalse = [
        '0', 'false', 'False', 'FALSE', 'n', 'N', 'no', 'No', 'NO', 'off', 'Off', 'OFF'
    ];

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function cast($type = null)
    {
        if (!isset($type)) {
            return $this->value;
        }
        $value = $this->value;
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
                return (new static($item))->cast($typeInArray);
            }, $value);
        }
        switch ($type) {
            case 'string':
                if (is_array($value)) {
                    return implode(',', $value);
                }
                break;
            case "num":
            case "number":
                $type = 'float';
                break;
            case "bool":
            case "boolean":
                if (in_array($value, $this->boolFalse)) {
                    $value = false;
                }
                break;
        }
        settype($value, $type);
        return $value;
    }
}
