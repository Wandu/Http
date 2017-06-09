<?php
namespace Wandu\Http\Exception;

use RuntimeException;

class CannotCallMethodException extends RuntimeException
{
    /** @var string */
    protected $methodName;
    
    /** @var string */
    protected $className;

    /**
     * @param string $methodName
     * @param string $className
     */
    public function __construct($methodName, $className = null)
    {
        $this->methodName = $methodName;
        $this->className = $className;
        $message = "cannot call {$methodName}";
        if ($className) {
            $message .= " in {$className}";
        }
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
