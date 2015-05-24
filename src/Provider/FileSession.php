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
     * @param string $id
     */
    public function __construct($path, $id)
    {
        $this->file = $file = "{$path}/{$id}";
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
