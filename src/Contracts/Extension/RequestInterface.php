<?php
namespace Wandu\Http\Contracts\Extension;

use Psr\Http\Message\RequestInterface as PsrRequestInterface;

interface RequestInterface extends PsrRequestInterface, MessageInterface
{
}
