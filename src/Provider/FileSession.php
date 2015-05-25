<?php
namespace Wandu\Session\Provider;

use Wandu\Session\SessionInterface;

class FileSession implements SessionInterface
{
    /** @var resource */
    private $file;

    /** @var array */
    private $dataSet = [];

    /**
     * @param string $path
     * @param string $sessionId
     */
    public function __construct($path, $sessionId)
    {
        $this->file = $file = "{$path}/{$sessionId}";
        if (file_exists($file)) {
            $this->dataSet = require $file;
        }
    }

    public function __destruct()
    {
        file_put_contents($this->file, '<?php return ' . var_export($this->dataSet, true) . ';');
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $this->dataSet[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return isset($this->dataSet[$name]) ? $this->dataSet[$name] : null;
    }
}
