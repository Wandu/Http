<?php
namespace Wandu\Http\Psr\Stream;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

class StringStream implements StreamInterface
{
    /** @var string */
    protected $context;

    /** @var int */
    protected $cursor = 0;

    /**
     * @param string $context
     */
    public function __construct($context = '')
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $this->rewind();
        return $this->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        throw new RuntimeException('can not use the close method.');
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        throw new RuntimeException('can not use the detach method.');
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return strlen($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return $this->cursor;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return $this->cursor === strlen($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->cursor = $offset;
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
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        $length = strlen($string);
        $this->context = substr_replace($this->context, $string, $this->cursor, $length);
        $this->cursor += $length;
        return $length;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        $result = substr($this->context, $this->cursor, $length);
        $this->cursor += $length;
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        if ($this->context === '') {
            return '';
        }
        $result = substr($this->context, $this->cursor);
        $this->cursor = strlen($this->context);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        return isset($key) ? null : [];
    }
}
