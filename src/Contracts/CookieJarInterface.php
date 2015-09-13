<?php
namespace Wandu\Http\Contracts;

use DateTime;

interface CookieJarInterface
{
    /**
     * @param string $name
     * @return string|null
     */
    public function get($name);

    /**
     * @param string $name
     * @param string $value
     * @param \DateTime $expire
     * @return self
     */
    public function set($name, $value, DateTime $expire = null);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @param string $name
     * @return self
     */
    public function remove($name);
}
