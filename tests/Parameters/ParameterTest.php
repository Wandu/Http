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

    public function testGetNull()
    {
        $params = new Parameter([
            'null' => null,
        ]);
        $this->assertNull($params->get('null', "Other Value!!"));
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

    public function testFallback()
    {
        $fallbacks = new Parameter([
            'string1' => 'string 1 fallback!',
            'fallback' => 'fallback!',
        ]);
        $params = new Parameter([
            'string1' => 'string 1!',
            'string2' => 'string 2!',
        ], $fallbacks);

        $this->assertSame("string 1!", $params->get('string1'));
        $this->assertSame("string 2!", $params->get('string2'));
        $this->assertSame("fallback!", $params->get('fallback'));
        $this->assertSame(null, $params->get('undefined'));

        $this->assertSame("string 1!", $params->get('string1', "default"));
        $this->assertSame("string 2!", $params->get('string2', "default"));
        $this->assertSame("fallback!", $params->get('fallback', "default"));
        $this->assertSame("default", $params->get('undefined', "default"));
    }

    /**
     * @dataProvider castingProvider
     */
    public function testCasting($input, $cast, $output)
    {
        $params = new Parameter([
            'array' => $input,
        ]);

        $this->assertSame($input, $params->get('array'));
        $this->assertSame($output, $params->get('array', [], ['cast' => $cast]));
    }

    public function castingProvider()
    {
        return [
            [['10', '20', '30'], 'int[]', [10, 20, 30]],
            [['10', '20', '30'], 'integer[]', [10, 20, 30]],
            [['10', '20', '30'], 'string[]', ['10', '20', '30']],
            [['10', '20', '30'], 'array', ['10', '20', '30']],
            [['10', '20', '30'], 'string', '10,20,30'],

            ['10,20,30', 'int[]', [10, 20, 30]],
            ['10,20,30', 'integer[]', [10, 20, 30]],
            ['10,20,30', 'string[]', ['10', '20', '30']],
            ['10,20,30', 'array', ['10', '20', '30']],
            ['10,20,30', 'string', '10,20,30'],

            ['10', 'int[]', [10]],
            ['10', 'integer[]', [10]],
            ['10', 'string[]', ['10']],
            ['10', 'array', ['10']],
            ['10', 'string', '10'],
        ];
    }
}
