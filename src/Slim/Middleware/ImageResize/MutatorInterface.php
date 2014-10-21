<?php

/*
 * This file is part of the Slim Image Resize middleware
 *
 * Copyright (c) 2014 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-image-resize
 *
 */

namespace Slim\Middleware\ImageResize;

interface MutatorInterface
{
    public static function regexp();
    public function options();
    public function execute();
    public function save($file);
    public function mime();
    public function encode();
}
