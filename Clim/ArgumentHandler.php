<?php

namespace Clim;

class ArgumentHandler extends Handler
{
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

    protected function evaluateBody($str)
    {
        $this->meta_var = trim($str);
    }
}