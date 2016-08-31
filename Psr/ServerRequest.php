<?php
namespace Wandu\Http\Psr;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Wandu\Http\Contracts\AttributeInterface;
use Wandu\Http\Traits\ServerRequestTrait;

class ServerRequest extends Request implements ServerRequestInterface
{
    use ServerRequestTrait;

    /**
     * ServerRequest constructor.
     * @param array $serverParams
     * @param array $queryParams
     * @param array $parsedBody
     * @param array $cookieParams
     * @param array $uploadedFiles
     * @param array $attributes
     * @param string $method
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param \Psr\Http\Message\StreamInterface|null $body
     * @param array $headers
     * @param string $protocolVersion
     */
    public function __construct(
        array $serverParams = [],
        array $queryParams = [],
        array $parsedBody = [],
        array $cookieParams = [],
        array $uploadedFiles = [],
        array $attributes = [],
        $method = null,
        $uri = null,
        StreamInterface $body = null,
        array $headers = [],
        $protocolVersion = '1.1'
    ) {
        $this->validArrayOfUploadedFiles($uploadedFiles);

        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->queryParams = $queryParams;
        $this->parsedBody = $parsedBody;
        $this->uploadedFiles = $uploadedFiles;
        $this->attributes = $attributes;

        parent::__construct($method, $uri, $body, $headers, $protocolVersion);
    }
    
    public function __sleep()
    {
        foreach ($this->attributes as $key => $attribute) {
            if ($attribute instanceof AttributeInterface) {
                $this->attributes[$key] = $attribute->getAttribute($this);
            }
        }
        return array_keys(get_object_vars($this));
    }
}
