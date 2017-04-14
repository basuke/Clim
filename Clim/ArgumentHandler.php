<?php

namespace Clim;

class ArgumentHandler extends Handler
{
    public function handle($argument, Context $context)
    {
        if (is_null($argument) || strlen($argument) == 0) {
            throw new Exception\ArgumentRequiredException($this->metaVar());
        }
        $context->push($argument, $this->metaVar());
    }

    protected function evaluateBody($str)
    {
        $this->_meta_var = trim($str);
    }
}