<?php
namespace Wandu\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use InvalidArgumentException;

class ServerRequest extends Request implements ServerRequestInterface
{
    /** @var array */
    protected $serverParams;

    /** @var array */
    protected $cookieParams;

    /** @var array */
    protected $queryParams;

    /** @var array */
    protected $uploadedFiles;

    /** @var array|object */
    protected $parsedBody;

    /** @var array */
    protected $attributes;

    /**
     * @param array $serverParams
     * @param array $cookieParams
     * @param array $queryParams
     * @param array $uploadedFiles
     * @param array|object $parsedBody
     * @param array $attributes
     * @param string $httpVersion
     * @param UriInterface $uri
     */
    public function __construct(
        array $serverParams = [],
        array $cookieParams = [],
        array $queryParams = [],
        array $uploadedFiles = [],
        $parsedBody = [],
        array $attributes = [],
        $httpVersion = '1.1',
        UriInterface $uri = null
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
            $this->initHeaders($serverParams),
            new Stream('php://input')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $this->validArrayOfUploadedFiles($uploadedFiles);
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        if (!isset($this->attributes[$name])) {
            return $default;
        }
        return $this->attributes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value)
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name)
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }

    /**
     * @param array $files
     * @throws InvalidArgumentException
     */
    protected function validArrayOfUploadedFiles(array $files)
    {
        foreach ($files as $file) {
            if (is_array($file)) {
                $this->validArrayOfUploadedFiles($file);
                continue;
            }
            if (!($file instanceof UploadedFileInterface)) {
                throw new InvalidArgumentException(
                    'Invalid uploaded files value. It must be a array of UploadedFileInterface.'
                );
            }
        }
    }

    /**
     * @param array $serverParams
     * @return array
     */
    protected function initHeaders(array $serverParams)
    {
        $headers = array();
        foreach ($serverParams as $key => $value) {
            if (strpos($key, 'HTTP_COOKIE') === 0) {
                // Cookies are handled using the $_COOKIE superglobal
                continue;
            }
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(substr($key, 5), '_', ' ');
                $name = strtr(ucwords(strtolower($name)), ' ', '-');
                $name = strtolower($name);
                $headers[$name] = explode(',', $value);
                continue;
            }
            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = substr($key, 8); // Content-
                $name = 'Content-' . (($name == 'MD5') ? $name : ucfirst(strtolower($name)));
                $name = strtolower($name);
                $headers[$name] = explode(',', $value);
                continue;
            }
        }
        return $headers;
    }
}
