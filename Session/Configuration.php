<?php
namespace Wandu\Http\Session;

class Configuration
{
    /** @var int */
    protected $timeout = 3600;
    
    /** @var string */
    protected $name = 'WdSessId';

    /** @var int */
    protected $gc_frequency = 100;
    
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getGcFrequency()
    {
        return $this->gc_frequency;
    }
    
    /**
     * @return string
     */
    public function getUniqueId()
    {
        return sha1($this->name . uniqid());
    }
}
