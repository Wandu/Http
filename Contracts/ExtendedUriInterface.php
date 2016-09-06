<?php
namespace Wandu\Http\Contracts;

use Psr\Http\Message\UriInterface;

interface ExtendedUriInterface extends UriInterface
{
    /**
     * @param \Psr\Http\Message\UriInterface $uriToJoin
     * @return \Wandu\Http\Contracts\ExtendedUriInterface
     */
    public function join(UriInterface $uriToJoin);

    /**
     * @param string $name
     * @param bool $emptyStrict
     * @return bool
     */
    public function hasQueryParam($name, $emptyStrict = false);

    /**
     * @param string $name
     * @param string $default
     * @param bool $emptyStrict
     * @return string
     */
    public function getQueryParam($name, $default = null, $emptyStrict = false);

    /**
     * @param string $name
     * @param string $value
     * @return \Wandu\Http\Contracts\ExtendedUriInterface
     */
    public function withQueryParam($name, $value);

    /**
     * @param string $name
     * @return \Wandu\Http\Contracts\ExtendedUriInterface
     */
    public function withoutQueryParam($name);
}
