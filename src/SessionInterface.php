<?php
namespace Wandu\Session;

interface SessionInterface
{
    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value);

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name);
}
