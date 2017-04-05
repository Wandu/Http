<?php
namespace Wandu\Http\Contracts;

use DateTime;

interface CookieJarInterface extends ParameterInterface
{
    /**
     * @param string $name
     * @param string $value
     * @param \DateTime $expire
     * @return self
     */
    public function set($name, $value, DateTime $expire = null);

    /**
     * @param string $name
     * @return self
     */
    public function remove($name);
}
