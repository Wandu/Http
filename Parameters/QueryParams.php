<?php
namespace Wandu\Http\Parameters;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Http\Contracts\QueryParamsInterface;

class QueryParams extends Parameter implements QueryParamsInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Wandu\Http\Contracts\ParameterInterface $fallback
     */
    public function __construct(ServerRequestInterface $request = null, ParameterInterface $fallback = null)
    {
        parent::__construct(
            $request ? $request->getQueryParams() : [],
            $fallback
        );
    }
}
