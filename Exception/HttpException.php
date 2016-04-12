<?php
namespace Wandu\Http\Exception;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Wandu\Http\Psr\Response;
use Wandu\Http\Traits\MessageTrait;
use Wandu\Http\Traits\ResponseTrait;

class HttpException extends Exception implements ResponseInterface
{
    use MessageTrait;
    use ResponseTrait;

    /** @var array */
    protected $attributes;

    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     * @param \Psr\Http\Message\StreamInterface $body
     * @param array $headers
     * @param string $protocolVersion
     * @param array $attributes
     */
    public function __construct(
        $statusCode = 500,
        $reasonPhrase = '',
        StreamInterface $body = null,
        array $headers = [],
        $protocolVersion = '1.1',
        array $attributes = []
    ) {
        $this->validStatusCode($statusCode);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $this->filterReasonPhrase($statusCode, $reasonPhrase);

        $this->body = $body;
        foreach ($headers as $name => $header) {
            $this->headerNames[strtolower($name)] = $name;
            $this->headers[$name] = $header;
        }
        $this->protocolVersion = $protocolVersion;
        $this->attributes = $attributes;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function toResponse()
    {
        return new Response(
            $this->getStatusCode(),
            $this->getBody(),
            $this->getHeaders(),
            $this->getReasonPhrase(),
            $this->getProtocolVersion()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        throw new RuntimeException("cannot change status in HttpException.");
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        throw new RuntimeException("cannot change protocolVersion in HttpException.");
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        throw new RuntimeException("cannot change header in HttpException.");
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        throw new RuntimeException("cannot change header in HttpException.");
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        throw new RuntimeException("cannot change header in HttpException.");
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        throw new RuntimeException("cannot change body in HttpException.");
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
}
