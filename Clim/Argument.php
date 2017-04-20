<?php

namespace Clim;

use Clim\Cli\ArgumentInterface;
use Clim\Cli\Component;
use Clim\Cli\Parameters;
use Clim\Helper\DeferredDefinitionTrait;

class Argument extends Component implements ArgumentInterface
{
    use DeferredDefinitionTrait;

    /**
     * @param string $definition
     * @param int $flags
     * @param \Closure|null $callable
     */
    public function __construct($definition, $flags = 0, $callable = null)
    {
        $this->definition = $definition;

        parent::__construct($flags, $callable);
    }

    public function handle($argument, Parameters $parameters, Context $context)
    {
        $this->needDefined();

        if (is_null($argument) || strlen($argument) == 0) {
            if (is_null($this->default)) {
                throw new Exception\ArgumentRequiredException($this->metaVar());
            }

            $argument = $this->default;
        }

        $context->push($argument, $this->metaVar());
    }

    protected function define($body, $name, $pattern, $note)
    {
        $this->meta_var = $body ?: $name;
    }
}