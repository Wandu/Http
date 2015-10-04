<?php
namespace Wandu\Http\Psr;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Wandu\Http\Traits\ServerRequestTrait;

class ServerRequest extends Request implements ServerRequestInterface
{
    use ServerRequestTrait;

    /**
     * @param array $serverParams
     * @param array $cookieParams
     * @param array $queryParams
     * @param array $uploadedFiles
     * @param array $parsedBody
     * @param array $attributes
     * @param string $method
     * @param \Psr\Http\Message\UriInterface|null $uri
     * @param string $protocolVersion
     * @param array $headers
     * @param \Psr\Http\Message\StreamInterface|null $body
     */
    public function __construct(
        array $serverParams = [],
        array $cookieParams = [],
        array $queryParams = [],
        array $uploadedFiles = [],
        array $parsedBody = [],
        array $attributes = [],
        $method = null,
        UriInterface $uri = null,
        $protocolVersion = '1.1',
        array $headers = [],
        StreamInterface $body = null
    ) {
        $this->validArrayOfUploadedFiles($uploadedFiles);

        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->queryParams = $queryParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->parsedBody = $parsedBody;
        $this->attributes = $attributes;

        parent::__construct($method, $uri, $protocolVersion, $headers, $body);
    }
}
