<?php
namespace Wandu\Http\Factory;

use Closure;
use Generator;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Traversable;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\Stream\IteratorStream;
use Wandu\Http\Psr\Stream\StringStream;

class ResponseFactory
{
    /** @var \Wandu\Http\Factory\ResponseFactory */
    public static $instance;
    
    /**
     * @param \Psr\Http\Message\StreamInterface|string $content
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create($content = null, $status = 200, array $headers = [])
    {
        if (isset($content) && !($content instanceof StreamInterface)) {
            $content = new StringStream($content);
        }
        return new Response($status, $content, $headers, '', '1.1');
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
        return $this->create($contents, $status, $headers);
    }

    /**
     * @param string|array $data
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function json($data = [], $status = 200, array $headers = [])
    {
        return $this->create(json_encode($data), $status, $headers)
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
        return $this->create(file_get_contents($file), 200, $headers)
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
}
