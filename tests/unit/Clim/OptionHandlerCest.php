<?php
namespace Clim;

use \UnitTester;

class OrderHandlerCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function test1(UnitTester $I)
    {
        $I->wantTo('see the handler matches with short option');

        $handler = new OptionHandler('-f');

        $I->assertTrue($handler->match('f'));
    }

    public function test2(UnitTester $I)
    {
        $I->wantTo('see the handler matches with long option');

        $handler = new OptionHandler('--foo');

        $I->assertTrue($handler->match('foo'));
    }

    public function test3(UnitTester $I)
    {
        $I->wantTo('see the handler matches with long option by defining mix');

        $handler = new OptionHandler('-f|--foo');

        $I->assertTrue($handler->match('f'));
        $I->assertTrue($handler->match('foo'));
        $I->assertFalse($handler->match('hello'));
    }

    public function test4(UnitTester $I)
    {
        $I->wantTo('see the handler matches with long option by defining mix');

        $handler = new OptionHandler('-f|--foo');

        $I->assertTrue($handler->match('f'));
        $I->assertTrue($handler->match('foo'));
        $I->assertFalse($handler->match('hello'));
    }

    public function test5(UnitTester $I)
    {
        $I->wantTo('see the handler matches with option with value');

        $handler = new OptionHandler('-t {VALUE}');

        $I->assertTrue($handler->needValue());
    }

    public function test6(UnitTester $I)
    {
        $I->wantTo('see the handler matches with mixed option with value');

        $handler = new OptionHandler('-t|--time {VALUE}');

        $I->assertTrue($handler->needValue());
    }

    public function test7(UnitTester $I)
    {
        $I->wantTo('see the handler works with option with value');

        $handler = new OptionHandler('-t|--time {TIME_STR}');
        $context = new Context([]);

        $I->assertTrue($handler->needValue());
        $I->assertEquals('TIME_STR', $handler->metaVar());

        $I->assertTrue($handler->handle('t', '42', $context));
        $I->assertEquals($context['t'], '42');
    }

    public function test8(UnitTester $I)
    {
        $I->wantTo('see the handler works with option with value from extra arguments collection');

        $handler = new OptionHandler('-t|--time {TIME_STR}');
        $context = new Context(['365']);

        $I->assertTrue($handler->handle('time', null, $context));
        $I->assertEquals($context['time'], '365');
        $I->assertFalse($context->hasNext());
    }

    public function test9(UnitTester $I)
    {
        $I->wantTo('see the handler works with option with value and pattern');

        $handler = new OptionHandler('-t|--time {TIME_STR|\\d+}');
        $context = new Context([]);

        $I->expectException(
            \Clim\Exception\OptionException::class,
            function () use ($handler, $context) {
                $handler->handle('time', '123abc', $context);
            });
    }
}
