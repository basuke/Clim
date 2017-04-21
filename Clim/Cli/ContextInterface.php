<?php

namespace Clim\Cli;

interface ContextInterface
{
    public function getResult();
    public function setResult($result);
}