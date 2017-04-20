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

    public function checkMultipleMiddlewares(UnitTester $I)
    {
        $stack = new MiddlewareStack();
        $stack->push(Kernel::sandwitch('<hello>', '<bye>'));
        $stack->push(Kernel::sandwitch('<foo>', '<bar>'));
        $context = new MiddlewareContext();

        $I->assertEquals([
            '<foo>',
            '<hello>',
            '<kernel>',
            '<bye>',
            '<bar>',
        ], $stack->run($context, new Kernel())->getResult());
    }
}

class Kernel
{
    public function __invoke(ContextInterface $context)
    {
        return "<kernel>";
    }

    public static function sandwitch($a, $b)
    {
        return function ($context, $next) use ($a, $b) {
            $context->setResult($a);
            $context = $next($context);
            $context->setResult($b);
            return $context;
        };
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

