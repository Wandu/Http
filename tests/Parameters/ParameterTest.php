<?php
namespace Wandu\Http\Parameters;

use Mockery;
use PHPUnit_Framework_TestCase;
use Wandu\Http\Contracts\ParameterInterfaceTestTrait;
use Wandu\Http\Support\CastProviderTrait;

class ParameterTest extends PHPUnit_Framework_TestCase
{
    use CastProviderTrait;
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

    public function testToArrayWithCasting()
    {
        $values = $this->castingProvider();

        // always true!!!!
        for ($i = 0; $i < 100; $i++) {
            $castKey1 = $values[rand(0, count($values) - 1)];
            $castKey2 = $values[rand(0, count($values) - 1)];

            $noCastKey1 = $values[rand(0, count($values) - 1)];
            $noCastKey2 = $values[rand(0, count($values) - 1)];

            $params = new Parameter([
                'key1' => $castKey1[0],
                'key2' => $castKey2[0],
                'key3' => $noCastKey1[0],
                'key4' => $noCastKey2[0],
            ]);

            $this->assertSame([
                'key1' => $castKey1[2],
                'key2' => $castKey2[2],
                'key3' => $noCastKey1[0],
                'key4' => $noCastKey2[0],
            ], $params->toArray([
                'key1' => $castKey1[1],
                'key2' => $castKey2[1],
            ]));
        }
    }
}
