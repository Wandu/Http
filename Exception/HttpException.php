<?php
namespace Wandu\Http\Exception;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use function Wandu\Http\response;

class HttpException extends Exception implements ResponseInterface
{
    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var array */
    protected $attributes;

    /**
     * @param mixed $response
     * @param array $attributes
     */
    public function __construct($response = null, array $attributes = [])
    {
        $this->response = response()->auto($response);
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
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
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
        return new HttpException(
            $this->response->withStatus($code, $reasonPhrase),
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        return new HttpException(
            $this->response->withProtocolVersion($version),
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        return new HttpException(
            $this->response->withHeader($name, $value),
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        return new HttpException(
            $this->response->withAddedHeader($name, $value),
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        return new HttpException(
            $this->response->withoutHeader($name),
            $this->attributes
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        return new HttpException(
            $this->response->withBody($body),
            $this->attributes
        );
    }
}
