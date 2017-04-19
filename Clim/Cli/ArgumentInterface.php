<?php

namespace Clim\Cli;

use Clim\Context;

interface ArgumentInterface
{
    public function handle($argument, Parameters $parameter, Context $context);
}