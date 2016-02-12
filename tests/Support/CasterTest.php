<?php
namespace Wandu\Http\Support;

use Mockery;
use PHPUnit_Framework_TestCase;

class CasterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider castingProvider
     */
    public function testCasting($input, $cast, $output)
    {
        if (is_object($output)) {
            $this->assertInstanceOf(\stdClass::class, (new Caster($input))->cast($cast));
            $this->assertEquals($output, (new Caster($input))->cast($cast));
        } else {
            $this->assertSame($output, (new Caster($input))->cast($cast));
        }
    }

    public function castingProvider()
    {
        return [
            [['10', '20', '30'], 'int[]', [10, 20, 30]],
            [['10', '20', '30'], 'integer[]', [10, 20, 30]],
            [['10', '20', '30'], 'string[]', ['10', '20', '30']],
            [['10', '20', '30'], 'array', ['10', '20', '30']],
            [['10', '20', '30'], 'string', '10,20,30'],
            [['10', '20', '30'], 'object', (object)(['10', '20', '30'])],

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

            ['10', 'int', 10],
            ['10', 'number', 10.0],
            ['10', 'float', 10.0],
            ['10', 'double', 10.0],
            ['10', 'bool', true],
            ['10', 'boolean', true],
            ['', 'boolean', false],
            ['true', 'boolean', true],
            ['false', 'boolean', false],
            ['off', 'boolean', false],
            ['Off', 'boolean', false],
            ['FALSE', 'boolean', false],
            ['No', 'boolean', false],
        ];
    }
}
