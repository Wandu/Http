<?php
namespace Wandu\Session;

use ArrayAccess;

interface DataSetInterface extends ArrayAccess
{
    /**
     * @param array $dataSet
     * @return \Wandu\Session\DataSetInterface
     */
    public static function fromArray(array $dataSet);

    /**
     * @return array
     */
    public function toArray();
}
