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
