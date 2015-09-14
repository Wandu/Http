<?php
namespace Wandu\Http\Extension;

use Wandu\Http\Contracts\Extension\ResponseInterface;
use Wandu\Http\Traits\ResponseTrait;

class Response extends Message implements ResponseInterface
{
    use ResponseTrait;
}
