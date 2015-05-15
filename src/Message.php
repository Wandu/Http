<?php
namespace Wandu\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use InvalidArgumentException;

class Message implements MessageInterface
{
    /** @var string */
    protected $protocolVersion = '1.1';

    /** @var array */
    protected $headers = [];

    /** @var array */
    protected $headerNames = [];

    /** @var StreamInterface */
    protected $body;

    /**
     * @param string $protocolVersion
     * @param array $headers
     * @param StreamInterface $body
     */
    public function __construct($protocolVersion, array $headers = [], StreamInterface $body = null)
    {
        $this->protocolVersion = $protocolVersion;
        foreach ($headers as $name => $header) {
            $this->headerNames[strtolower($name)] = $name;
            $this->headers[$name] = $header;
        }
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        return $this->hasHeader($name) ?
            $this->headers[$this->headerNames[strtolower($name)]] :
            [];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        $values = $this->filterHeaderValue($value);

        $new = clone $this;
        $new->headerNames[strtolower($name)] = $name;
        $new->headers[$name] = $values;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $values = $this->filterHeaderValue($value);
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }
        $new = clone $this;
        $headerName = $new->headerNames[strtolower($name)];
        $new->headers[$headerName] = array_merge($this->headers[$headerName], $values);
        return $new;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    /**
     * @param mixed $value
     * @return array
     * @throws InvalidArgumentException
     */
    protected function filterHeaderValue($value)
    {
        if (is_string($value)) {
            $value = [$value];
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
