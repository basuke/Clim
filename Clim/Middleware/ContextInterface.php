<?php

namespace Clim\Middleware;

interface ContextInterface
{
    public function getResult();
    public function setResult($result);
}