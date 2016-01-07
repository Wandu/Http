<?php
namespace Wandu\Http\Parameters;

use Mockery;
use PHPUnit_Framework_TestCase;

class ParameterTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $params = new Parameter([
            'string' => 'string!',
            'number' => '10',
        ]);

        $this->assertSame('string!', $params->get('string'));
        $this->assertSame('10', $params->get('number'));

        $this->assertNull($params->get('string.undefined'));
        $this->assertNull($params->get('number.undefined'));
    }

    public function testGetWithDefault()
    {
        $params = new Parameter([
            'string' => 'string!',
            'number' => '10',
        ]);

        $this->assertSame("default", $params->get('string.undefined', "default"));
        $this->assertSame("default", $params->get('number.undefined', "default"));
    }

    public function testCasting()
    {
        $params = new Parameter([
            'array' => ['10', '20', '30'],
        ]);

        $this->assertSame(['10', '20', '30'], $params->get('array'));
        $this->assertSame([10, 20, 30], $params->get('array', [], ['cast' => 'int[]']));
    }
}
