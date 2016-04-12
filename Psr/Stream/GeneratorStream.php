<?php
namespace Wandu\Http\Psr\Stream;

use Closure;
use Generator;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class GeneratorStream implements StreamInterface
{
    /** @var \Closure */
    protected $handler;

    /** @var \Generator */
    protected $generator;

    /** @var bool */
    protected $isEof = false;

    /**
     * @param \Closure $handler
     */
    public function __construct(Closure $handler)
    {
        $generator = $handler();
        if (!($generator instanceof Generator)) {
            throw new InvalidArgumentException('first parameter must be closure that contain generator(yield syntax).');
        }
        $this->handler = $handler;
        $this->generator = $generator;
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
     * @return \Generator
     */
    public function getGenerator()
    {
        return $this->generator;
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
        throw new RuntimeException('GeneratorStream cannot getSize.');
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        throw new RuntimeException('GeneratorStream cannot tell.');
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
        throw new RuntimeException('GeneratorStream cannot seek.');
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->generator = $this->handler->__invoke();
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
        throw new RuntimeException('GeneratorStream cannot write.');
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
        throw new RuntimeException('GeneratorStream cannot read.');
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $contents = '';
        if (!$this->eof()) {
            foreach ($this->generator as $value) {
                $contents .= $value;
            }
            $this->isEof = true;
        }
        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $metadata = [
            'eof' => $this->eof(),
            'stream_type' => 'generator',
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
