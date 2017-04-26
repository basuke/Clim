<?php
$I = new UnitTester($scenario);
$I->wantTo('perform actions and see result');

$app = new \Clim\App();
$container = $app->getContainer();

$container['hello'] = function () {
    return "Hello World!!!";
};

class SimpleArray
{
    public function __construct($array)
    {
    }
}

$app->task(function ($hello, array $arguments, \Clim\Helper\Hash $opts, SimpleArray $options) use ($I) {
    $I->assertEquals("Hello World!!!", $hello);
    $I->assertTrue(is_array($arguments));
    $I->assertTrue($opts instanceof \Clim\Helper\Hash);
    $I->assertTrue($options instanceof SimpleArray);
    return 42;
});

$context = $app->runner()->run(['prog_name']);

$I->assertEquals(42, $context->getResult());
