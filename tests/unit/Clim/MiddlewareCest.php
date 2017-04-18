<?php

use Clim\Middleware\ContextInterface;
use Clim\Middleware\MiddlewareStack;

class MiddlewareCest
{
    public function checkBasicInvocation(UnitTester $I)
    {
        $stack = new MiddlewareStack();
        $context = new MiddlewareContext();

        $I->assertEquals([
            '<kernel>'
        ], $stack->run($context, new Kernel())->getResult());
    }
}

class Kernel
{
    public function __invoke(ContextInterface $context)
    {
        return "<kernel>";
    }
}

class MiddlewareContext implements ContextInterface
{
    public $response = [];

    public function getResult()
    {
        return $this->response;
    }

    public function setResult($result)
    {
        return $this->response[] = $result;
    }
}

