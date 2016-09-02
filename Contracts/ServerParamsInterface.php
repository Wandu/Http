<?php
namespace Wandu\Http\Contracts;

interface ServerParamsInterface extends ParameterInterface
{
    /**
     * @return boolean
     */
    public function isAjax();

    /**
     * @param string|array $contentType
     * @return boolean
     */
    public function accepts($contentType);

    /**
     * @return array
     */
    public function getLanguages();
    
    /**
     * @return string
     */
    public function getIpAddress();
}
