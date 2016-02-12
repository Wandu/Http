<?php
namespace Wandu\Http\Support;

use Mockery;
use PHPUnit_Framework_TestCase;

class CasterTest extends PHPUnit_Framework_TestCase
{
    use CastProviderTrait;

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
}
