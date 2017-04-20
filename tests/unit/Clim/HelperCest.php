<?php
namespace Clim;
use Clim\Helper\Hash;
use \UnitTester;

class HelperCest
{
    public function testSimpleHash(UnitTester $I)
    {
        $hash = new Hash();

        $I->assertFalse(isset($hash['foo']));
        $hash['foo'] = 'Hello';
        $I->assertEquals('Hello', $hash['foo']);

        $hash->append('foo', 'World');
        $I->assertEquals(['Hello', 'World'], $hash['foo']);

        $hash->append('bar', 123);
        $I->assertEquals([123], $hash['bar']);

        $I->assertEquals([
            'foo' => ['Hello', 'World'],
            'bar' => [123],
        ], $hash->all());
    }

    public function testHashPush(UnitTester $I)
    {
        $hash = new Hash();

        $hash->push(42);

        $hash['foo'] = 'bar';

        $I->assertEquals([
            42,
            'foo' => 'bar',
        ], $hash->all());
    }

    public function testHashSet(UnitTester $I)
    {
        $hash = new Hash();

        $hash->set('foo', 42);
        $hash->set('foo', 4200);
        $hash->set('bar', 42, true);
        $hash->set('bar', 4200, true);

        $I->assertEquals([
            'foo' => 4200,
            'bar' => [42, 4200],
        ], $hash->all());
    }
}
