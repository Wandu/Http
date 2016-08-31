<?php
namespace Wandu\Http\Attribute;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Contracts\AttributeInterface;

class LazyAttribute implements AttributeInterface
{
    /** @var callable */
    protected $handler;
    
    /**
     * @param callable $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(ServerRequestInterface $request)
    {
        return call_user_func($this->handler, $request);
    }
}
