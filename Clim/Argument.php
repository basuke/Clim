<?php

namespace Clim;

use Clim\Cli\ArgumentInterface;
use Clim\Cli\Component;
use Clim\Cli\Parameters;
use Clim\Helper\DeferredDefinitionTrait;

class Argument extends Component implements ArgumentInterface
{
    use DeferredDefinitionTrait;

    protected $multiple;

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

        $name = $this->metaVar();
        $context->push($argument, $name, $this->multiple);

        if ($this->multiple) {
            while ($parameters->hasMore()) {
                $kind = $parameters->nextKind();
                if ($kind == Parameters::KIND_OPTION_LONG || $kind == Parameters::KIND_OPTION_SHORT) break;
                $context->push($parameters->next(), $name, true);
            }
        }
    }

    public function multiple($flag = true)
    {
        $this->multiple = $flag;
    }

    protected function define($body, $name, $pattern, $note)
    {
        $this->meta_var = $body ?: $name;
    }
}