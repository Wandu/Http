<?php
namespace Wandu\Http\Psr\Stream;

use Psr\Http\Message\StreamInterface;
use Wandu\Http\Psr\Stream;

class PhpInputStream extends StringStream implements StreamInterface
{
    public function __construct()
    {
        $stream = new Stream('php://input');
        parent::__construct($stream->__toString());
    }
}
