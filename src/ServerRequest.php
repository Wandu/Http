<?php
namespace Wandu\Http;

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
     * @param array|object $parsedBody
     * @param array $attributes
     * @param string $httpVersion
     * @param UriInterface $uri
     * @param StreamInterface $body
     */
    public function __construct(
        array $serverParams = [],
        array $cookieParams = [],
        array $queryParams = [],
        array $uploadedFiles = [],
        $parsedBody = [],
        array $attributes = [],
        $httpVersion = '1.1',
        UriInterface $uri = null,
        StreamInterface $body = null
    ) {
        $this->validArrayOfUploadedFiles($uploadedFiles);

        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->queryParams = $queryParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->parsedBody = $parsedBody;
        $this->attributes = $attributes;

        parent::__construct(
            $httpVersion,
            isset($serverParams['REQUEST_METHOD']) ? $serverParams['REQUEST_METHOD'] : 'GET',
            $uri,
            $this->getHeadersFromServerParams($serverParams),
            isset($body) ? $body : new Stream('php://input')
        );
        if (strpos($this->getHeaderLine('content-type'), 'application/json') === 0) {
            $this->parsedBody = json_decode($this->body->__toString(), true);
        }
    }
}
