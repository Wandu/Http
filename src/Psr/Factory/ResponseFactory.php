<?php
namespace Wandu\Http\Psr\Factory;

use Closure;
use InvalidArgumentException;
use Wandu\Http\Psr\Response;
use Wandu\Http\Psr\Stream\StringStream;

class ResponseFactory
{
    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create($content = null, $status = 200, array $headers = [])
    {
        return new Response(
            $status,
            '',
            '1.1',
            $headers,
            isset($content) ? new StringStream($content) : null
        );
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
     * @param  string|array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function json($data = [], $status = 200, array $headers = [])
    {
        return $this->create(json_encode($data), $status, $headers)
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param  string  $file
     * @param  string  $name
     * @param  array  $headers
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
     * @param string $path
     * @param int $status
     * @param array $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function redirect($path, $status = 302, $headers = [])
    {
        return $this->create(null, $status, $headers)
            ->withStatus($status)
            ->withAddedHeader('Location', $path);
    }
}
