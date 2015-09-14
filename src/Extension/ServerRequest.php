<?php
namespace Wandu\Http\Extension;

use Wandu\Http\Contracts\Extension\ServerRequestInterface;
use Wandu\Http\Traits\ServerRequestTrait;

class ServerRequest extends Request implements ServerRequestInterface
{
    use ServerRequestTrait;
}
