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
     * @param bool $isStrict
     * @return array
     */
    public function getMany(array $keyOrDefaults = [], $isStrict = false);

    /**
     * @param string $key
     * @param mixed $default
     * @param bool $isStrict
     * @return mixed
     */
    public function get($key, $default = null, $isStrict = false);

    /**
     * @param string $key
     * @return boolean
     */
    public function has($key);
}
