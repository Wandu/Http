<?php
namespace Wandu\Http\Traits;

use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;

trait ServerRequestTrait
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
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * @param array $cookies
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param array $query
     * @return static
     */
    public function withQueryParams(array $query)
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    /**
     * @return array
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @param array $uploadedFiles
     * @return static
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $this->validArrayOfUploadedFiles($uploadedFiles);
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }

    /**
     * @return array
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @param null|array|object $parsedBody
     * @return static
     */
    public function withParsedBody($parsedBody)
    {
        $new = clone $this;
        $new->parsedBody = $parsedBody;
        return $new;
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
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function withAttribute($name, $value)
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    /**
     * @param string $name
     * @return static
     */
    public function withoutAttribute($name)
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }

    /**
     * @param array $files
     * @throws \InvalidArgumentException
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
}
