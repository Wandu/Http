<?php
namespace Wandu\Http\Psr\Factory;

use Psr\Http\Message\StreamInterface;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Http\Psr\Stream;
use Wandu\Http\Psr\Uri;

class ServerRequestFactory
{
    /** @var \Wandu\Http\Psr\Factory\UploadedFileFactory */
    protected $fileFactory;

    /**
     * @param \Wandu\Http\Psr\Factory\UploadedFileFactory $fileFactory
     */
    public function __construct(UploadedFileFactory $fileFactory)
    {
        $this->fileFactory = $fileFactory;
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function fromGlobals()
    {
        return $this->factory($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES, new Stream('php://input'));
    }

    /**
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $cookies
     * @param array $files
     * @param \Psr\Http\Message\StreamInterface $stream
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function factory(
        array $server,
        array $get,
        array $post,
        array $cookies,
        array $files,
        StreamInterface $stream = null
    ) {
        if (!isset($stream)) {
            $stream = new Stream('php://memory');
        }
        $headers = $this->getHeadersFromServerParams($server);

        if (isset($headers['content-type']) && strpos($headers['content-type'][0], 'application/json') === 0) {
            $post = json_decode($stream->__toString(), true);
        }
        return new ServerRequest(
            $server,
            $cookies,
            $get,
            $this->fileFactory->fromFiles($files),
            $post,
            [],
            isset($server['REQUEST_METHOD']) ? $server['REQUEST_METHOD'] : 'GET',
            $this->getUri($server),
            '1.1',
            $headers,
            $stream
        );
    }

    /**
     * @param array $server
     * @return \Wandu\Http\Psr\Uri
     */
    protected function getUri(array $server)
    {
        $stringUri = $this->getHostAndPort($server);
        if ($stringUri !== '') {
            $stringUri = $this->getScheme($server) . '://' . $stringUri;
        }
        $stringUri .= $this->getRequestUri($server);
        return new Uri($stringUri);
    }

    /**
     * @param array $server
     * @return string
     */
    protected function getScheme(array $server)
    {
        if ((isset($server['HTTPS']) && $server['HTTPS'] !== 'off')
            || (isset($server['HTTP_X_FORWAREDED_PROTO']) && $server['HTTP_X_FORWAREDED_PROTO'] === 'https')) {
            return 'https';
        }
        return 'http';
    }

    /**
     * @param array $server
     * @return string
     */
    protected function getHostAndPort(array $server)
    {
        if (isset($server['HTTP_HOST'])) {
            return $server['HTTP_HOST'];
        }
        if (!isset($server['SERVER_NAME'])) {
            return '';
        }
        $host = $server['SERVER_NAME'];
        if (isset($server['SERVER_PORT'])) {
            $host .= ':' . $server['SERVER_PORT'];
        }
        return $host;
    }

    /**
     * @param array $server
     * @return string
     */
    protected function getRequestUri(array $server)
    {
        if (isset($server['REQUEST_URI'])) {
            return $server['REQUEST_URI'];
        }
        return '/';
    }

    /**
     * @param array $serverParams
     * @return array
     */
    protected function getHeadersFromServerParams(array $serverParams)
    {
        $headers = array();
        foreach ($serverParams as $key => $value) {
            if (strpos($key, 'HTTP_COOKIE') === 0) {
                // Cookies are handled using the $_COOKIE superglobal
                continue;
            }
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(substr($key, 5), '_', ' ');
                $name = strtr(ucwords(strtolower($name)), ' ', '-');
                $name = strtolower($name);
                $headers[$name] = explode(',', $value);
                continue;
            }
            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = substr($key, 8); // Content-
                $name = 'Content-' . (($name == 'MD5') ? $name : ucfirst(strtolower($name)));
                $name = strtolower($name);
                $headers[$name] = explode(',', $value);
                continue;
            }
        }
        return $headers;
    }
}
