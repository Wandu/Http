<?php
namespace Wandu\Http\Psr\Factory;

use Psr\Http\Message\StreamInterface;
use Wandu\Http\Psr\ServerRequest;
use Wandu\Http\Psr\Stream;
use Wandu\Http\Psr\Stream\PhpInputStream;
use Wandu\Http\Psr\Uri;

class ServerRequestFactory
{
    use HelperTrait;

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
     * @deprecated
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function fromGlobals()
    {
        return $this->createFromGlobals();
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function createFromGlobals()
    {
        return $this->factory($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES, new PhpInputStream());
    }

    /**
     * @param string $body
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function fromSocketBody($body)
    {
        $lines = array_map('trim', explode("\n", $body));
        $blankKey = array_search('', $lines);

        $phpServer = $this->getPhpServerValuesFromPlainHeader(array_slice($lines, 0, $blankKey));

        return $this->factory($phpServer, [], [], [], []);
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
        $headers = $this->getPsrHeadersFromServerParams($server);

        // exists body, but not exists posts
        $bodyContent = $stream->__toString();
        if ($bodyContent !== '' && count($post) === 0) {
            if (isset($headers['content-type'])) {
                // do not define multipart/form-data
                // because, it use only in POST method.
                // ref. en: https://issues.apache.org/jira/browse/FILEUPLOAD-197#comment-13595136
                // ref. kr: https://blog.outsider.ne.kr/1001
                if (strpos($headers['content-type'][0], 'application/json') === 0) {
                    $jsonBody = json_decode($bodyContent, true);
                    $post = $jsonBody ? $jsonBody : $post;
                } elseif (strpos($headers['content-type'][0], 'application/x-www-form-urlencoded') === 0) {
                    parse_str($bodyContent, $post);
                }
            }
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
            $stream,
            $headers,
            '1.1'
        );
    }

    /**
     * Parse plain headers.
     *
     * @param array $plainHeaders
     * @return array
     */
    protected function getPhpServerValuesFromPlainHeader(array $plainHeaders)
    {
        $httpInformation = explode(' ', array_shift($plainHeaders));
        $servers = [
            'REQUEST_METHOD' => $httpInformation[0],
            'REQUEST_URI' => $httpInformation[1],
            'SERVER_PROTOCOL' => $httpInformation[2],
        ];
        foreach ($plainHeaders as $plainHeader) {
            list($key, $value) = array_map('trim', explode(':', $plainHeader, 2));
            $servers['HTTP_' . strtoupper(str_replace('-', '_', $key))] = $value;
        }
        return $servers;
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
}
