<?php

namespace Clim;

use Clim\Cli\ArgumentInterface;
use Clim\Cli\Component;
use Clim\Helper\DeferredDefinitionTrait;
use Psr\Container\ContainerInterface;

class Dispatcher extends Component implements ArgumentInterface
{
    use DeferredDefinitionTrait;

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
        $this->definition = $definition;

        parent::__construct();

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
        $child = new Builder($this->container);

        call_user_func($builder, $child);

        $child->runner()->run($context);
        return true;
    }

    protected function define($body, $name, $pattern, $note)
    {
        $this->meta_var = $name;
    }
}