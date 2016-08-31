<?php
namespace Wandu\Http\Contracts;

use ArrayAccess;
use JsonSerializable;

interface ParameterInterface extends ArrayAccess, JsonSerializable
{
    /**
     * @param \Wandu\Http\Contracts\ParameterInterface $fallback
     * @return \Wandu\Http\Contracts\ParameterInterface|null
     */
    public function setFallback(ParameterInterface $fallback);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @param array $keyOrDefaults
     * @return array
     */
    public function getMany(array $keyOrDefaults = []);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param string $key
     * @return boolean
     */
    public function has($key);
}
