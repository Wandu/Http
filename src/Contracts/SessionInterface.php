<?php
namespace Wandu\Http\Contracts;

use ArrayAccess;

interface SessionInterface extends ArrayAccess, ParameterInterface
{
    /**
     * @return string
     */
    public function getId();

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
     * @return array
     */
    public function getRawParams();

    /**
     * @param string $name
     * @return self
     */
    public function remove($name);
}
