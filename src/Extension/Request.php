<?php
namespace Wandu\Http\Extension;

use Wandu\Http\Contracts\Extension\RequestInterface;
use Wandu\Http\Traits\RequestTrait;

class Request extends Message implements RequestInterface
{
    use RequestTrait;
}
