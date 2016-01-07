<?php
namespace Wandu\Http\Parameters;

interface ParameterInterface
{
    /**
     * @param string $key
     * @param mixed $default
     * @param array $option
     * @return mixed
     */
    public function get($key, $default = null, array $option = []);
}
