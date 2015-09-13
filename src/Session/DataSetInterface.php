<?php
namespace Wandu\Http\Session;

use ArrayAccess;

interface DataSetInterface extends ArrayAccess
{
    /**
     * @param array $dataSet
     * @return \Wandu\Http\Session\DataSetInterface
     */
    public static function fromArray(array $dataSet);

    /**
     * @return array
     */
    public function toArray();
}
