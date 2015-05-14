<?php
namespace Wandu\Http;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

trait RequestTrait
{
    /** @var string */
    protected $method;

    /** @var string */
    protected $requestTarget;

    /** @var UriInterface */
    protected $uri;

    /** @var array */
    protected $validMethods = [
        'CONNECT' => true,
        'DELETE' => true,
        'GET' => true,
        'HEAD' => true,
        'OPTIONS' => true,
        'PATCH' => true,
        'POST' => true,
        'PUT' => true,
        'TRACE' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget()
    {
        if (isset($this->requestTarget)) {
            return $this->requestTarget;
        }
        if (!isset($this->uri)) {
            return '/';
        }
        $target = $this->uri->getPath();
        if ($this->uri->getQuery() !== '') {
            $target .= '?' . $this->uri->getQuery();
        }
        if ($target === '') {
            $target = '/';
        }
        return $target;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $new = clone $this;
        $new->uri = $uri;

        if ($preserveHost || ('' === $host = $uri->getHost())) {
            return $new;
        }
        if ('' !== $port = $uri->getPort()) {
            $host .= ':' . $port;
        }
        $new->headers['Host'] = [$host];
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method)
    {
        $new = clone $this;
        $new->method = $this->filterMethod($method);
        return $new;
    }

    /**
     * @param mixed $method
     * @return string
     */
    protected function filterMethod($method)
    {
        if ($method === null) {
            return '';
        }
        if (!is_string($method)) {
            throw new InvalidArgumentException('Unsupported HTTP method. It must be a string.');
        }
        $method = strtoupper($method);
        if (!isset($this->validMethods[$method])) {
            throw new InvalidArgumentException("Unsupported HTTP method. \"{$method}\" provided.");
        }
        return $method;
    }
}
