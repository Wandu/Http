<?php
namespace Wandu\Http\Factory;

use Closure;
use Generator;
use InvalidArgumentException;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use Traversable;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\Stream\IteratorStream;
use Wandu\Http\Psr\Stream\ResourceStream;
use Wandu\Http\Psr\Stream\StringStream;

class ResponseFactory
{
    /** @var \Wandu\Http\Factory\ResponseFactory */
    public static $instance;

    /**
     * @param mixed $contents
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function auto($contents = null, $status = 200, array $headers = [])
    {
        if ($contents instanceof ResponseInterface) {
            return $contents;
        }
        if ($contents instanceof StreamInterface || !isset($contents)) {
            return $this->create($contents, $status, $headers);
        }
        while (is_callable($contents)) {
            $nextResponse = call_user_func($contents);
            $contents = $nextResponse;
        }
        // int, float, boolean, string
        if (is_scalar($contents)) {
            if ($contents === true) {
                $contents = 'true';
            } elseif ($contents === false) {
                $contents = 'false';
            }
            return $this->string((string)$contents, $status, $headers);
        }
        if (is_array($contents)) {
            return $this->json($contents, $status, $headers);
        }
        if (is_object($contents)) {
            if ($contents instanceof Traversable) {
                return $this->iterator($contents, $status, $headers);
            } elseif (method_exists($contents, '__toString')) {
                return $this->string($contents, $status, $headers);
            } else {
                return $this->json($contents, $status, $headers);
            }
        }
        if (is_resource($contents)) {
            return $this->resource($contents, $status, $headers);
        }
        throw new RuntimeException('unsupported type of response.');
    }
    
    /**
     * @param \Psr\Http\Message\StreamInterface $stream
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create(StreamInterface $stream = null, $status = 200, array $headers = [])
    {
        return new Response($status, $stream, $headers, '', '1.1');
    }

    /**
     * @param \Closure $area
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function capture(Closure $area, $status = 200, array $headers = [])
    {
        ob_start();
        $area();
        $contents = ob_get_contents();
        ob_end_clean();
        return $this->string($contents, $status, $headers);
    }

    /**
     * @param string $contents
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function string(string $contents, $status = 200, array $headers = [])
    {
        return $this->create(new StringStream($contents), $status, $headers);
    }
    
    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function json($data = [], $status = 200, array $headers = [])
    {
        return $this->create(new StringStream(json_encode($data)), $status, $headers)
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param string $file
     * @param string $name
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function download($file, $name = null, array $headers = [])
    {
        if (!is_file($file)) {
            new InvalidArgumentException("\"{$file}\" is not a file.");
        }
        return $this->create(new StringStream(file_get_contents($file)), 200, $headers)
            ->withHeader('Pragma', 'public')
            ->withHeader('Expires', '0')
            ->withHeader('Content-Type', 'application/octet-stream')
            ->withHeader(
                'Content-Disposition',
                'attachment; filename=' . (isset($name) ? $name : basename($file))
            )
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Content-Length', filesize($file) . '');
    }

    /**
     * @param \Psr\Http\Message\UriInterface|string $path
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function redirect($path, $status = 302, $headers = [])
    {
        if ($path instanceof UriInterface) {
            $path = $path->__toString();
        }
        return $this->create(null, $status, $headers)
            ->withStatus($status)
            ->withAddedHeader('Location', $path);
    }

    /**
     * @deprecated use iterator
     * 
     * @param \Generator $generator
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function generator(Generator $generator, $status = 200, array $headers = [])
    {
        return $this->create(new Iteratorstream($generator), $status, $headers);
    }

    /**
     * @param \Traversable $iterator
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function iterator(Traversable $iterator, $status = 200, array $headers = [])
    {
        return $this->create(new IteratorStream($iterator), $status, $headers);
    }

    /**
     * @param resource $resource
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function resource($resource, $status = 200, array $headers = [])
    {
        return $this->create(new ResourceStream($resource), $status, $headers);
    }
}
