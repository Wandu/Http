<?php
namespace Wandu\Http\Contracts;

interface ParameterInterface
{
    /**
     * @param array $casts
     * @return array
     */
    public function toArray(array $casts = []);

    /**
     * @param string $key
     * @param mixed $default
     * @param string $cast
     * @return mixed
     */
    public function get($key, $default = null, $cast = null);

    /**
     * @param string $key
     * @return boolean
     */
    public function has($key);
}
