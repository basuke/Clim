<?php
namespace Clim;

use UnitTester;
use Clim\Helper\MethodAliasTrait;

class Hello
{
    use MethodAliasTrait;

    public function hello($count)
    {
        return str_repeat('hi', $count);
    }

    protected $alias_of_konnichiwa = 'hello';
}

class MethodAliasCest
{
    public function seeMethodAliasWork(UnitTester $I)
    {
        $h = new Hello();

        $I->assertEquals($h->hello(3), 'hihihi');
        $I->assertEquals($h->konnichiwa(2), 'hihi');
    }
}
