<?php
namespace Wandu\Http\Contracts;

trait ParameterInterfaceTestTrait
{
    public function testGet()
    {
        $params = $this->param1;

        $this->assertSame('string!', $params->get('string'));
        $this->assertSame('10', $params->get('number'));

        $this->assertNull($params->get('string.undefined'));
        $this->assertNull($params->get('number.undefined'));
    }

    public function testGetNull()
    {
        $params = $this->param2;

        $this->assertNull($params->get('null', "Other Value!!"));
    }

    public function testHas()
    {
        $params = $this->param1;

        $this->assertTrue($params->has('string'));
        $this->assertTrue($params->has('number'));

        $this->assertFalse($params->has('string.undefined'));
        $this->assertFalse($params->has('number.undefined'));
    }

    public function testHasNull()
    {
        $params = $this->param2;

        $this->assertTrue($params->has('null'));
    }

    public function testToArray()
    {
        $params = $this->param2;

        $this->assertSame([
            'null' => null,
        ], $params->toArray());
    }

    public function testToArrayWithFallback()
    {
        $params = $this->param3;

        $this->assertSame([
            'string1' => 'string 1!',
            'string2' => 'string 2!',
            'fallback' => 'fallback!',
        ], $params->toArray());
    }

    public function testGetWithDefault()
    {
        $params = $this->param1;

        $this->assertSame("default", $params->get('string.undefined', "default"));
        $this->assertSame("default", $params->get('number.undefined', "default"));
    }

    public function testFallback()
    {
        $params = $this->param3;

        $this->assertSame("string 1!", $params->get('string1'));
        $this->assertSame("string 2!", $params->get('string2'));
        $this->assertSame("fallback!", $params->get('fallback'));
        $this->assertSame(null, $params->get('undefined'));

        $this->assertSame("string 1!", $params->get('string1', "default"));
        $this->assertSame("string 2!", $params->get('string2', "default"));
        $this->assertSame("fallback!", $params->get('fallback', "default"));
        $this->assertSame("default", $params->get('undefined', "default"));
    }

    public function testHasWithFallback()
    {
        $params = $this->param3;

        $this->assertTrue($params->has('string1'));
        $this->assertTrue($params->has('string2'));
        $this->assertTrue($params->has('fallback'));
        $this->assertFalse($params->has('undefined'));
    }


    public function testGetMany()
    {
        $params = $this->param1;

        $this->assertSame(
            [
                'string' => 'string!',
                'number' => '10'
            ],
            $params->getMany(['string', 'number'])
        );

        $this->assertSame(
            [
                'string' => 'string!',
            ],
            $params->getMany(['string'])
        );

        $this->assertSame(
            [
                'string' => 'string!',
            ],
            $params->getMany(['string', 'unknown'])
        );

        $this->assertSame(
            [
                'string' => 'string!',
                'unknown' => null,
            ],
            $params->getMany(['string', 'unknown' => null])
        );

        $this->assertSame(
            [
                'string' => 'string!',
                'unknown' => false,
            ],
            $params->getMany(['string' => false, 'unknown' => false])
        );
    }
}
