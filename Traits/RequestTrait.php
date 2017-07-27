<?php
namespace Wandu\Http\Traits;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

trait RequestTrait
{
    /** @var string */
    protected $method;

    /** @var \Psr\Http\Message\UriInterface */
    protected $uri;

    /** @var string */
    protected $requestTarget;

    /**
     * @return string
     */
    public function getRequestTarget()
    {
        if (isset($this->requestTarget) && $this->requestTarget !== '') {
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
     * @param string $requestTarget
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    /**
     * @return \Psr\Http\Message\UriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param \Psr\Http\Message\UriInterface $uri
     * @param bool $preserveHost
     * @return static
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
        $new->headerNames['host'] = 'Host';
        $new->headers['Host'] = [$host];
        return $new;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return static
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
        return strtoupper($method);
    }
}
