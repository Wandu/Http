<?php
namespace Wandu\Http\Psr;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    /** @var string */
    private $scheme;

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $userInfo;

    /** @var string */
    private $path;

    /** @var string */
    private $query;

    /** @var string */
    private $fragment;

    /** @var array */
    protected static $allowedSchemes = [
        'http'  => 80,
        'https' => 443,
    ];

    /**
     * @param string $uri
     */
    public function __construct($uri)
    {
        $parsedUrl = parse_url(rawurldecode($uri));

        $this->scheme = isset($parsedUrl['scheme']) ? $this->filterScheme($parsedUrl['scheme']) : '';
        $this->host = isset($parsedUrl['host']) ? $this->filterHost($parsedUrl['host']) : '';
        $this->port = isset($parsedUrl['port']) ? $parsedUrl['port'] : null;

        $this->userInfo = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        if (isset($parsedUrl['pass'])) {
            $this->userInfo .= ':' . $parsedUrl['pass'];
        }

        $this->path = isset($parsedUrl['path']) ? $this->filterPath($parsedUrl['path']) : '';
        $this->query = isset($parsedUrl['query']) ? $this->filterQuery($parsedUrl['query']) : '';
        $this->fragment = isset($parsedUrl['fragment']) ? $this->filterFragment($parsedUrl['fragment']) : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority()
    {
        if ($this->host === '') {
            return '';
        }
        $authority = $this->host;
        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->isNonstandardPort()) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        if ($this->isNonstandardPort()) {
            return $this->port;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme)
    {
        $new = clone $this;
        $new->scheme = $this->filterScheme($scheme);
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $userInfo = $user;
        if (isset($password)) {
            $userInfo .= ':' . $password;
        }
        $new = clone $this;
        $new->userInfo = $userInfo;
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host)
    {
        $new = clone $this;
        $new->host = $this->filterHost($host);
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port)
    {
        $new = clone $this;
        if (!isset($port)) {
            $new->port = null;
        } else {
            $port = (int) $port;
            if ($port < 1 || $port > 65535) {
                throw new InvalidArgumentException("Invalid port \"{$port}\". It must be a valid TCP/UDP port.");
            }
            $new->port = $port;
        }
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path)
    {
        if (!is_string($path)) {
            $path = (is_object($path) ? get_class($path) : gettype($path));
            throw new InvalidArgumentException("Invalid path \"{$path}\". It must be a string.");
        }
        if (strpos($path, '?') !== false) {
            throw new InvalidArgumentException("Invalid path \"{$path}\". It must not contain a query string.");
        }
        if (strpos($path, '#') !== false) {
            throw new InvalidArgumentException("Invalid path \"{$path}\". It must not contain a URI fragment.");
        }
        $new = clone $this;
        $new->path = $this->filterPath($path);
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query)
    {
        if (!is_string($query)) {
            $query = (is_object($query) ? get_class($query) : gettype($query));
            throw new InvalidArgumentException("Invalid query \"{$query}\". It must be a string.");
        }
        if (strpos($query, '#') !== false) {
            throw new InvalidArgumentException("Invalid query \"{$query}\". It must not contain a URI fragment.");
        }
        $new = clone $this;
        $new->query = $this->filterQuery($query);
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment)
    {
        $new = clone $this;
        $new->fragment = $this->filterFragment($fragment);
        return $new;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $uri = '';
        if ('' !== $this->scheme) {
            $uri .= "{$this->scheme}://";
        }
        if ('' !== $authority = $this->getAuthority()) {
            $uri .= $authority;
        }
        if ('' !== $this->path) {
            if ($uri !== '' && $this->path[0] !== '/' && $this->getAuthority() !== '') {
                $uri .= '/';
            }
            $uri .= "{$this->path}";
        }
        if ('' !== $this->query) {
            $uri .= "?{$this->query}";
        }
        if ('' !== $this->fragment) {
            $uri .= "#{$this->fragment}";
        }
        return $uri;
    }

    /**
     * @param Uri $uriToJoin
     * @return Uri
     */
    public function join(Uri $uriToJoin)
    {
        // other host
        if ($uriToJoin->scheme !== '' || $uriToJoin->host !== '') {
            return clone $uriToJoin;
        }

        $uriToReturn = clone $this;

        // current path.
        if ($uriToJoin->path === '' || $uriToJoin->path === '.') {
            return $uriToReturn;
        }

        $newPathItems = explode('/', $uriToReturn->path);

        $pathItemToJoin = explode('/', $uriToJoin->path);
        if (isset($pathItemToJoin[0])) {
            array_pop($newPathItems);
        }
        $newPathItems = array_merge($newPathItems, $pathItemToJoin);
        $pathItemsToReturn = [];
        foreach ($newPathItems as $item) {
            if ($item === '') {
                $pathItemsToReturn = [$item];
            } elseif ($item === '.') {
                continue;
            } elseif ($item === '..') {
                array_pop($pathItemsToReturn);
            } else {
                array_push($pathItemsToReturn, $item);
            }
        }
        if (isset($pathItemsToReturn[0]) && $pathItemsToReturn[0] !== '') {
            array_unshift($pathItemsToReturn, '');
        }

        $uriToReturn->path = implode('/', $pathItemsToReturn);
        $uriToReturn->query = $uriToJoin->query;
        $uriToReturn->fragment = $uriToJoin->fragment;

        return $uriToReturn;
    }

    /**
     * @param string $scheme
     * @return string
     */
    protected function filterScheme($scheme)
    {
        $scheme = rtrim(strtolower($scheme), ':/');
        if ($scheme === '') {
            return '';
        }
        if (!isset(static::$allowedSchemes[$scheme])) {
            throw new InvalidArgumentException("Unsupported scheme \"{$scheme}\".");
        }
        return $scheme;
    }

    /**
     * @param string $host
     * @return string
     */
    protected function filterHost($host)
    {
        return strtolower($host);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function filterPath($path)
    {
        $items = explode('/', $path);
        foreach ($items as $idx => $item) {
            $items[$idx] = rawurlencode($item);
        }
        return implode('/', $items);
    }

    /**
     * @param string $query
     * @return string
     */
    protected function filterQuery($query)
    {
        $items = explode('&', $query);
        foreach ($items as $idx => $item) {
            $pair = explode('=', $item, 2);
            $items[$idx] = $this->filterFragment($pair[0]);
            if (count($pair) === 2) {
                $items[$idx] .= '=' . $this->filterFragment($pair[1]);
            }
        }
        return implode('&', $items);
    }

    /**
     * @reference https://github.com/phly/http/blob/master/src/Uri.php
     * @param string $fragment
     * @return string
     */
    protected function filterFragment($fragment)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $fragment
        );
    }

    /**
     * @return bool
     */
    protected function isNonstandardPort()
    {
        return isset($this->port) &&
        isset(static::$allowedSchemes[$this->scheme]) &&
        static::$allowedSchemes[$this->scheme] !== $this->port;
    }
}
