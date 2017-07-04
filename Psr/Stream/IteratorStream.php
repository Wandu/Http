<?php
namespace Wandu\Http\Psr\Stream;

use IteratorAggregate;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Traversable;

class IteratorStream implements StreamInterface, IteratorAggregate
{
    /** @var \Iterator */
    protected $iterator;

    /** @var string */
    protected $cachedContents;

    /** @var bool */
    protected $isEof = false;

    /**
     * @param \Traversable $iterator
     */
    public function __construct(Traversable $iterator)
    {
        $this->iterator = $iterator;
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
    public function getIterator()
    {
        return $this->iterator;
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
        throw new RuntimeException('IteratorStream cannot getSize.');
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        throw new RuntimeException('IteratorStream cannot tell.');
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return $this->isEof;
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        throw new RuntimeException('IteratorStream cannot seek.');
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->isEof = false;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        throw new RuntimeException('IteratorStream cannot write.');
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        throw new RuntimeException('IteratorStream cannot read.');
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        if (!$this->eof()) {
            if (!isset($this->cachedContents)) {
                $contents = '';
                foreach ($this->iterator as $value) {
                    $contents .= $value;
                }
                $this->cachedContents = $contents;
            }
            $this->isEof = true;
            return $this->cachedContents;
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $metadata = [
            'eof' => $this->eof(),
            'stream_type' => 'iterator',
            'seekable' => false
        ];
        if (!isset($key)) {
            return $metadata;
        }
        if (!array_key_exists($key, $metadata)) {
            return null;
        }
        return $metadata[$key];
    }
}
