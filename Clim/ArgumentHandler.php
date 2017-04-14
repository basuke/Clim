<?php

namespace Clim;

class ArgumentHandler extends Handler
{
    public function handle($argument, Context $context)
    {
        $context->push($argument, $this->metaVar());
    }

    protected function evaluateBody($str)
    {
        $this->_meta_var = trim($str);
    }
}