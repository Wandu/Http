<?php
namespace Wandu\Http\Parameters;

interface ParameterInterface
{
    /**
     * @return array
     */
    public function getAll();

    /**
     * @param string $key
     * @param mixed $default
     * @param array $option
     * @return mixed
     */
    public function get($key, $default = null, array $option = []);
}
