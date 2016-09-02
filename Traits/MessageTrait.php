<?php
namespace Wandu\Http\Traits;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /** @var string */
    protected $protocolVersion = '1.1';

    /** @var array */
    protected $headers = [];

    /** @var array */
    protected $headerNames = [];

    /** @var \Psr\Http\Message\StreamInterface */
    protected $body;

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $version
     * @return static
     */
    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    /**
     * @param string $name
     * @return array
     */
    public function getHeader($name)
    {
        return $this->hasHeader($name) ?
            $this->headers[$this->headerNames[strtolower($name)]] : [];
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine($name)
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return static
     */
    public function withHeader($name, $value)
    {
        $lowerName = strtolower($name);
        $values = $this->filterHeaderValue($value, $lowerName);

        $new = clone $this;
        $new->headerNames[$lowerName] = $name;
        $new->headers[$name] = $values;
        return $new;
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return static
     */
    public function withAddedHeader($name, $value)
    {
        $lowerName = strtolower($name);
        $values = $this->filterHeaderValue($value, $lowerName);
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }
        $new = clone $this;
        $headerName = $new->headerNames[$lowerName];
        $new->headers[$headerName] = array_merge($this->headers[$headerName], $values);
        return $new;
    }

    /**
     * @param string $name
     * @return string
     */
    public function withoutHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return clone $this;
        }
        $normalized = strtolower($name);
        $headerName = $this->headerNames[$normalized];

        $new = clone $this;
        unset($new->headerNames[$normalized], $new->headers[$headerName]);
        return $new;
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param \Psr\Http\Message\StreamInterface $body
     * @return static
     */
    public function withBody(StreamInterface $body)
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    /**
     * @param string|array $value
     * @param string $name
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function filterHeaderValue($value, $name = null)
    {
        if (is_string($value)) {
            if ($name === 'user-agent') {
                $value = [$value];
            } else {
                $value = array_map(function ($item) {
                    return trim($item);
                }, explode(',', $value));
            }
        }
        if (!is_array($value) || !$this->isArrayOfString($value)) {
            throw new InvalidArgumentException('Invalid header value. It must be a string or array of strings.');
        }
        return $value;
    }

    /**
     * @param array $values
     * @return bool
     */
    protected function isArrayOfString(array $values)
    {
        foreach ($values as $value) {
            if (!is_string($value)) {
                return false;
            }
        }
        return true;
    }
}
