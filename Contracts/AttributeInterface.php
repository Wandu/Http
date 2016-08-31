<?php
namespace Wandu\Http\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface AttributeInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return mixed
     */
    public function getAttribute(ServerRequestInterface $request);
}
