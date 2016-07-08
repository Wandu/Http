<?php
namespace Wandu\Http\Exception;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Wandu\Http\Psr\Response;

abstract class AbstractHttpException extends Exception implements ResponseInterface
{
    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var array */
    protected $attributes;

    /**
     * @param int $statusCode
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $attributes
     */
    public function __construct(
        $statusCode,
        ResponseInterface $response = null,
        array $attributes = []
    ) {
        if (!isset($response)) {
            $response = new Response();
        }
        $this->response = $response->withStatus($statusCode);
        $this->attributes = $attributes;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->response->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {
        return $this->response->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        return $this->response->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->response->getReasonPhrase();
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        throw new RuntimeException("cannot change status in " . static::class . ".");
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        throw new RuntimeException("cannot change protocolVersion in " . static::class . ".");
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        throw new RuntimeException("cannot change header in " . static::class . ".");
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        throw new RuntimeException("cannot change header in " .  static::class . ".");
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        throw new RuntimeException("cannot change header in " . static::class . ".");
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        throw new RuntimeException("cannot change body in " . static::class . ".");
    }
}
