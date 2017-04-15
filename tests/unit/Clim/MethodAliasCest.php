<?php
namespace Clim;
use \UnitTester;
use \Clim\Traits\MethodAlias;

class Hello
{
    use MethodAlias;

    public function hello($count)
    {
        return str_repeat('hi', $count);
    }

    public function alias__konnichiwa($count)
    {
        return $this->hello($count);
    }
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
