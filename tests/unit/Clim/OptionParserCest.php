<?php
namespace Clim;

use \UnitTester;

class OptionParserCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function test1(UnitTester $I)
    {
        $I->wantTo('see the parser matches with short option');

        $parser = new OptionParser('-f');

        $I->assertTrue($parser->match('f'));
    }

    public function test2(UnitTester $I)
    {
        $I->wantTo('see the parser matches with long option');

        $parser = new OptionParser('--foo');

        $I->assertTrue($parser->match('foo'));
    }

    public function test3(UnitTester $I)
    {
        $I->wantTo('see the parser matches with long option by defining mix');

        $parser = new OptionParser('-f|--foo');

        $I->assertTrue($parser->match('f'));
        $I->assertTrue($parser->match('foo'));
        $I->assertFalse($parser->match('hello'));
    }

    public function test4(UnitTester $I)
    {
        $I->wantTo('see the parser matches with long option by defining mix');

        $parser = new OptionParser('-f|--foo');

        $I->assertTrue($parser->match('f'));
        $I->assertTrue($parser->match('foo'));
        $I->assertFalse($parser->match('hello'));
    }

    public function test5(UnitTester $I)
    {
        $I->wantTo('see the parser matches with option with value');

        $parser = new OptionParser('-t {VALUE}');

        $I->assertTrue($parser->needValue());
    }

    public function test6(UnitTester $I)
    {
        $I->wantTo('see the parser matches with mixed option with value');

        $parser = new OptionParser('-t|--time {VALUE}');

        $I->assertTrue($parser->needValue());
    }

    public function test7(UnitTester $I)
    {
        $I->wantTo('see the parser works with option with value');

        $parser = new OptionParser('-t|--time {TIME_STR}');
        $context = new Context([]);

        $I->assertTrue($parser->needValue());
        $I->assertEquals('TIME_STR', $parser->metaVar());

        $context->tentative('42');
        $I->assertTrue($parser->parse('t', $context));
        $I->assertEquals($context['t'], '42');
    }

    public function test8(UnitTester $I)
    {
        $I->wantTo('see the parser works with option with value from extra arguments collection');

        $parser = new OptionParser('-t|--time {TIME_STR}');
        $context = new Context(['365']);

        $I->assertTrue($parser->parse('time', $context));
        $I->assertEquals($context['time'], '365');
        $I->assertFalse($context->hasNext());
    }

    public function test9(UnitTester $I)
    {
        $I->wantTo('see the parser works with option with value and pattern');

        $parser = new OptionParser('-t|--time {TIME_STR|\\d+}');
        $context = new Context([]);

        $I->expectException(
            \Clim\Exception\OptionException::class,
            function () use ($parser, $context) {
                $context->tentative('123abc');
                $parser->parse('time', $context);
            });
    }
}
