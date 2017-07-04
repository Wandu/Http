<?php
namespace Wandu\Http\Psr\Stream;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Wandu\Http\Psr\Stream;
use RuntimeException;

class ResourceStream implements StreamInterface
{
    /** @var resource */
    protected $resource;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (!is_resource($resource) || get_resource_type($resource) !== 'stream') {
            throw new InvalidArgumentException("Argument 1 must be of the type stream resource.");
        }
        $this->resource = $resource;
    }
    
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if (!$this->isReadable()) {
            return '';
        }
        try {
            $this->rewind();
            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (!$this->resource) {
            return;
        }
        $resource = $this->detach();
        fclose($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        if (!isset($this->resource)) {
            return null;
        }
        return fstat($this->resource)['size'];
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        $this->isAvailableAndException();
        $result = ftell($this->resource);
        if (!is_int($result)) {
            throw new RuntimeException('Error occurred during tell operation');
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        if (!$this->resource) {
            return true;
        }
        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        if (!$this->resource) {
            return false;
        }
        return stream_get_meta_data($this->resource)['seekable'];
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->isAvailableAndException();
        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }
        if (0 !== fseek($this->resource, $offset, $whence)) {
            throw new RuntimeException('Error seeking within stream.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        if (!$this->resource) {
            return false;
        }
        $mode = stream_get_meta_data($this->resource)['mode'];
        return strpos($mode, 'w') !== false || strpos($mode, '+') !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        $this->isAvailableAndException();
        if (!$this->isWritable()) {
            throw new RuntimeException('Stream is not writable.');
        }
        if (false === $result = fwrite($this->resource, $string)) {
            throw new RuntimeException('Error writing to stream.');
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        if (!$this->resource) {
            return false;
        }
        $mode = stream_get_meta_data($this->resource)['mode'];
        return strpos($mode, 'r') !== false || strpos($mode, '+') !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        $this->isAvailableAndException();
        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable.');
        }
        if (false === $result = fread($this->resource, $length)) {
            throw new RuntimeException('Error reading from stream.');
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        if (!$this->isReadable()) {
            return '';
        }
        if (false === $result = stream_get_contents($this->resource)) {
            throw new RuntimeException('Error reading from stream.');
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $metaData = stream_get_meta_data($this->resource);
        if (!isset($key)) {
            return $metaData;
        }
        return isset($metaData[$key]) ? $metaData[$key] : null;
    }

    /**
     * @throws \RuntimeException
     */
    protected function isAvailableAndException()
    {
        if (!$this->resource) {
            throw new RuntimeException('No resource available.');
        }
    }
}
