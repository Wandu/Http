<?php
namespace Wandu\Http\Extension;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;

interface ServerRequestInterface extends PsrServerRequestInterface, HasCookieInterface, HasSessionInterface
{
}
