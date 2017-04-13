<?php
namespace Clim;
use \UnitTester;

class ContextCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function test1(UnitTester $I)
    {
        $I->wantToTest("a Context accepts `argv` and accessible as argv().");

        $context = new Context(['Hello', 'world']);
        $I->assertEquals($context->argv(), ['Hello', 'world']);
    }
}
