<?php

namespace Clim\Cli;

use Clim\Context;

interface ArgumentInterface
{
    public function handle($argument, Context $context);
}