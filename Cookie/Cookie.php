<?php
namespace Wandu\Http\Cookie;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

/**
 * @ref Symfony HttpFoundation Cookie
 */
class Cookie
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $value;

    /** @var \DateTime */
    protected $expire;

    /** @var string */
    protected $path;

    /** @var string */
    protected $domain;

    /** @var bool */
    protected $secure;

    /** @var bool */
    protected $httpOnly;

    /**
     * @param string $name
     * @param string $value
     * @param \DateTime $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     */
    public function __construct(
        $name,
        $value = null,
        DateTime $expire = null,
        $path = '/',
        $domain = null,
        $secure = false,
        $httpOnly = true
    ) {
        if (!$name) {
            throw new InvalidArgumentException('The cookie name cannot be empty.');
        }
        if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            throw new InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
        }
        $this->name = $name;
        $this->value = $value;
        $this->expire = $expire;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \DateTime
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * @return int
     */
    public function getMaxAge()
    {
        return max(-1, $this->expire->getTimestamp() - time());
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @return boolean
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $stringToReturn = urlencode($this->name).'=';
        if ($this->value) {
            $stringToReturn .= urlencode($this->value);
            if (isset($this->expire)) {
                $expire = $this->expire->setTimezone(new DateTimeZone('GMT'))->format("l, d-M-Y H:i:s T");
                $maxAge = $this->getMaxAge();
                $stringToReturn .= "; Expires={$expire}; Max-Age={$maxAge}";
            }
        } else {
            $stringToReturn .= 'deleted; Expires=Thursday, 01-Jan-1970 00:00:00 GMT; Max-Age=0';
        }
        if (isset($this->path)) {
            $stringToReturn .= '; Path=' . $this->path;
        }
        if (isset($this->domain)) {
            $stringToReturn .= '; Domain=' . $this->domain;
        }
        if ($this->isSecure()) {
            $stringToReturn .= '; Secure';
        }
        if ($this->isHttpOnly()) {
            $stringToReturn .= '; HttpOnly';
        }
        return $stringToReturn;
    }
}
