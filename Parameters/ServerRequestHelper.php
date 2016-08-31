<?php
namespace Wandu\Http\Parameters;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Support\Exception\CannotCallMethodException;

class ServerRequestHelper
{
    /** @var \Psr\Http\Message\ServerRequestInterface */
    protected $request;
    
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function isAjax()
    {
        return $this->request->hasHeader('x-requested-with') &&
            $this->request->getHeaderLine('x-requested-with') === 'XMLHttpRequest';
    }

    /**
     * @param string $customHeaderName
     * @return string
     */
    public function getIpAddress($customHeaderName = null)
    {
        // fix
        return $this->get('HTTP_X_FORWARDED_FOR', $this->get('REMOTE_ADDR'));
    }
}
