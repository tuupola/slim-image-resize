<?php

namespace Slim\Middleware\ImageResize;

interface MutatorInterface
{
    public function execute();
    public function save();
}
