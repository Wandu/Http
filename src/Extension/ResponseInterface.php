<?php
namespace Wandu\Http\Extension;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends PsrResponseInterface, MessageInterface
{
}
