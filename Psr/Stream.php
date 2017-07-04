<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Wandu\Http\Psr\Stream\ResourceStream;

class Stream extends ResourceStream implements StreamInterface
{
    /**
     * @param string $stream
     * @param string $mode
     * @throws \InvalidArgumentException
     */
    public function __construct($stream = 'php://memory', $mode = 'r')
    {
        $resource = @fopen($stream, $mode);
        if (!$resource) {
            throw new InvalidArgumentException(
                "Invalid stream \"{$stream}\". It must be a valid path with valid permissions."
            );
        }
        parent::__construct($resource);
    }
}
