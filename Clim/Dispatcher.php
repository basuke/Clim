<?php

namespace Clim;

use \Psr\Container\ContainerInterface;

class Dispatcher extends Handler
{
    /** @var array */
    protected $children = [];

    /** @var ContainerInterface $container */
    private $container;

    /**
     * @param string $definition
     * @param arrat $children
     */
    public function __construct($definition, array $children, ContainerInterface $container)
    {
        parent::__construct($definition);

        $this->children = $children;
        $this->container = $container;
    }

    public function handle($argument, Context $context)
    {
        $this->needDefined();

        if (!array_key_exists($argument, $this->children)) {
            throw new Exception("invalid subcommand");
        }

        $builder = $this->children[$argument];
        $child = new App($this->container);

        call_user_func($builder, $child);

        $child->runWithContext($context);
        return true;
    }
}