<?php
namespace Wandu\Http\Parameters;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Http\Contracts\ParameterInterface;
use Wandu\Http\Contracts\ServerParamsInterface;

class ServerParams extends Parameter implements ServerParamsInterface 
{
    /**
     * @reference https://www.sitepoint.com/web-foundations/mime-types-complete-list/
     * @reference https://msdn.microsoft.com/ko-kr/library/microsoft.reportingservices.reportrendering.image.mimetype%28v=sql.120%29.aspx  (for IE)
     * @reference http://sunwebexpert.com/books/detail/php/ie-non-standard-image-mime-type.html (for IE)
     * @var array
     */
    protected static $mimeTypes = [
        'html' => ['text/html', 'application/xhtml+xml'],
        'txt' => ['text/plain'],
        'js' => ['application/x-javascript', 'application/javascript', 'application/ecmascript', 'text/javascript', 'text/ecmascript'],
        'css' => ['application/x-pointplus', 'text/css'],
        'json' => ['application/json', 'application/x-json'],
        'xml' => ['text/xml', 'application/xml', 'application/x-xml'],
        'atom' => ['application/atom+xml'],
        'rss' => ['application/rss+xml'],
        'form' => ['application/x-www-form-urlencoded'],
    ];
    
    /** @var \Psr\Http\Message\ServerRequestInterface */
    protected $request;
    
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Wandu\Http\Contracts\ParameterInterface $fallback
     */
    public function __construct(ServerRequestInterface $request, ParameterInterface $fallback = null)
    {
        $this->request = $request;
        parent::__construct($request->getServerParams(), $fallback);
    }

    /**
     * {@inheritdoc}
     */
    public function isAjax()
    {
        return $this->request->hasHeader('x-requested-with') &&
            $this->request->getHeaderLine('x-requested-with') === 'XMLHttpRequest';
    }

    /**
     * {@inheritdoc}
     */
    public function getIpAddress($customHeaderName = null)
    {
        return $this->request->getHeaderLine('x-forwarded-for') ?: $this->get('REMOTE_ADDR');
    }

    /**
     * @reference https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @reference https://developer.mozilla.org/en-US/docs/Web/HTTP/Content_negotiation/List_of_default_Accept_values
     * {@inheritdoc}
     */
    public function accepts($contentTypes)
    {
        if (is_string($contentTypes) && array_key_exists($contentTypes, static::$mimeTypes)) {
            $contentTypes = static::$mimeTypes[$contentTypes];
        } elseif (is_string($contentTypes)) {
            $contentTypes = [$contentTypes];
        }
        $accepts = array_filter(explode(',', $this->request->getHeaderLine('accept')));
        if (count($accepts) === 0) return true;
        foreach ($accepts as $accept) {
            list($acceptType, $acceptSubType) = $this->splitType($accept);
            if ($acceptType === '*') return true;
            foreach ($contentTypes as $type) {
                if ($type === $accept) return true;
                list($checkType, $checkSubType) = $this->splitType($type);
                if ($acceptSubType === '*' && $checkType === $acceptType) return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages()
    {
        return array_map(function ($language) {
            return strtok($language, ';');
        }, explode(',', $this->request->getHeaderLine('accept-language')));
    }

    /**
     * @param string $type
     * @return array
     */
    private function splitType($type)
    {
        $acceptTypes = explode('/', strtok($type, ';'));
        if (!isset($acceptTypes[1])) $acceptTypes[1] = '*';
        return $acceptTypes;
    }
}
