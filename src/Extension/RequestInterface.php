<?php
namespace Wandu\Http\Extension;

use Psr\Http\Message\RequestInterface as PsrRequestInterface;

interface RequestInterface extends PsrRequestInterface, HasCookieInterface, HasSessionInterface
{
}
