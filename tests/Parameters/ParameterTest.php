<?php
namespace Wandu\Http\Parameters;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Http\Contracts\ParameterInterfaceTestTrait;

class ParameterTest extends PHPUnit_Framework_TestCase
{
    use ParameterInterfaceTestTrait;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param1;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param2;

    /** @var \Wandu\Http\Contracts\ParameterInterface */
    protected $param3;

    public function setUp()
    {
        $this->param1 = new Parameter([
            'string' => 'string!',
            'number' => '10',
        ]);
        $this->param2 = new Parameter([
            'null' => null,
        ]);

        $this->param3 = new Parameter([
            'string1' => 'string 1!',
            'string2' => 'string 2!',
        ], new Parameter([
            'string1' => 'string 1 fallback!',
            'fallback' => 'fallback!',
        ]));
    }
}
