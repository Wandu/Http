<?php
namespace Wandu\Http\Contracts;

interface SessionInterface extends ParameterInterface
{
    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function set($name, $value);

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function flash($name, $value);

    /**
     * @param string $name
     * @return self
     */
    public function remove($name);
}
