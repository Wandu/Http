<?php
namespace Wandu\Http\Contracts\Extension;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends PsrResponseInterface, MessageInterface
{
}
