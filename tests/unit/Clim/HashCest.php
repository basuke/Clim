<?php
namespace Clim;
use Clim\Helper\Hash;
use UnitTester;

class HashCest
{
    public function testEmptyHash(UnitTester $I)
    {
        $hash = new Hash();
        $I->assertEquals([], $hash->all());
    }

    public function testInitialArray(UnitTester $I)
    {
        $hash = new Hash([1, 2, 3]);
        $I->assertEquals([1, 2, 3], $hash->all());
    }

    public function testMergeHash(UnitTester $I)
    {
        $hash1 = new Hash([1, 2, 3]);
        $hash2 = new Hash([4, 5, 6]);
        $I->assertEquals([1, 2, 3, 4, 5, 6], $hash1->update($hash2->all())->all());
    }

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

    public function testHashConstructor(UnitTester $I)
    {
        $hash = new Hash([
            'hello' => 'world',
            'secret' => null,
        ], [
            'secret' => 42,
            'foo' => 'bar',
        ]);

        $I->assertEquals([
            'hello' => 'world',
            'foo' => 'bar',
            'secret' => 42,
        ], $hash->all());
    }

    public function testHashUpdate(UnitTester $I)
    {
        $hash = new Hash([
            'hello' => 'world',
            'secret' => null,
        ]);

        $hash->update([
            'secret' => 42,
            'foo' => 'bar',
        ]);

        $I->assertEquals([
            'hello' => 'world',
            'foo' => 'bar',
            'secret' => 42,
        ], $hash->all());
    }

    public function testSubArrayUpdate(UnitTester $I)
    {
        $hash = new Hash([
            'a1' => [
                0, 1, 2,
                'foo' => 'bar',
                'hello' => 'world',
            ],
            'a2' => 123,
            'a3' => [1, 2, 3],
        ]);

        $hash->update([
            'a1' => [
                3, 4, 5,
                'foo' => 'bingo',
                'zoo' => 123,
            ],
            'a2' => [456],
            'a3' => 456,
        ]);

        $I->assertEquals([
            'a1' => [
                0, 1, 2, 3, 4, 5,
                'foo' => 'bingo',
                'hello' => 'world',
                'zoo' => 123,
            ],
            'a2' => [456],
            'a3' => 456,
        ], $hash->all());
    }
}
