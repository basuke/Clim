<?php

namespace Clim;

use Clim\Helper\DeferredDefinitionTrait;

class ArgumentHandler extends Handler
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

    public function handle($argument, Context $context)
    {
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
        if ($body) $this->meta_var = $body;
        if ($name) $this->meta_var = $name;
        if ($pattern) $this->pattern = $pattern;
    }
}