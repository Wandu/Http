<?php
namespace Wandu\Cookie;

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

    /** @var int */
    protected $expire;

    /** @var array */
    protected $meta;

    /** @var array  */
    static protected $options = [
        'secure',
        'httponly'
    ];

    /** @var array */
    static protected $keys = [
        'path' => 'Path',
        'secure' => 'Secure',
        'httponly' => 'HttpOnly',
        'domain' => 'Domain',
    ];

    /**
     * @param string $name
     * @param string $value
     * @param string $expireAsTimeStamp
     * @param array $meta
     */
    public function __construct($name, $value = null, $expireAsTimeStamp = null, $meta = [])
    {
        if (!$name) {
            throw new InvalidArgumentException('The cookie name cannot be empty.');
        }
        // from PHP source code
        if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            throw new InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
        }
        $this->name = $name;
        $this->value = $value;
        $this->expire = $expireAsTimeStamp;
        $this->meta = $meta + [
                'path' => '/',
                'secure' => false,
                'httponly' => true,
            ];
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
                $stringToReturn .= '; Expires='.gmdate('D, d-M-Y H:i:s T', $this->expire);
            }
        } else {
            $stringToReturn .= 'deleted; Expires='.gmdate('D, d-M-Y H:i:s T', 0);
        }
        foreach ($this->meta as $key => $value) {
            $lowerKey = strtolower($key);
            $key = static::$keys[$lowerKey];
            if (in_array($lowerKey, static::$options)) {
                if ($value) {
                    $stringToReturn .= "; {$key}";
                }
            } else {
                $stringToReturn .= "; {$key}={$value}";
            }
        }
        return $stringToReturn;
    }
}
