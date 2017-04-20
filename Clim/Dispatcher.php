<?php

namespace Clim;

use Clim\Cli\ArgumentInterface;
use Clim\Cli\Component;
use Clim\Cli\Parameters;
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
     * @param array $children
     * @param ContainerInterface $container
     */
    public function __construct($definition, array $children, ContainerInterface $container)
    {
        $this->definition = $definition;

        parent::__construct();

        $this->children = $children;
        $this->container = $container;
    }

    public function handle($argument, Parameters $parameters, Context $context)
    {
        $this->needDefined();

        if (!array_key_exists($argument, $this->children)) {
            throw new \Exception("invalid sub command");
        }

        $builder = $this->children[$argument];
        $child = new App($this->container);

        call_user_func($builder, $child);

        $child->runner()->run($parameters->dumpOut(), $context);
        return true;
    }

    protected function define($body, $name, $pattern, $note)
    {
        $this->meta_var = $name;
    }
}