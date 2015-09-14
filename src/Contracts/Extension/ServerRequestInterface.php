<?php
namespace Wandu\Http\Contracts\Extension;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;

interface ServerRequestInterface extends PsrServerRequestInterface, RequestInterface
{
}
