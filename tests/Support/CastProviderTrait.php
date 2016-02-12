<?php
namespace Wandu\Http\Support;

trait CastProviderTrait
{
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