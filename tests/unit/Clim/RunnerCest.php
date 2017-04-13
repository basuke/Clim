<?php
namespace Clim;

use \UnitTester;

class ParserCest
{
    public function _before(UnitTester $I)
    {
        $this->handlers = [
            (new OptionHandler('-a')),
            (new OptionHandler('-b|--boo')),
            (new OptionHandler('--c')),
            (new OptionHandler('-d|-e|--do|--else')),
            (new OptionHandler('-f|--foo {FOO}')),
            (new OptionHandler('-g')),
            (new OptionHandler('-h')),
        ];
        $this->parser = new Parser($this->handlers);
    }

    public function _after(UnitTester $I)
    {
    }

    public function test1(UnitTester $I)
    {
        $I->wantTo('test result with an argument');

        list($opts, $argv) = $this->parser->parse(['hello', 'Basuke']);

        $I->assertEquals($opts->all(), []);
        $I->assertEquals($argv->all(), ['Basuke']);
    }
}
