<?php

/*
 * Automagical image resizing from Slim
 *
 * Copyright (c) 2013-2014 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-image-resize
 *
 */

namespace Slim\Middleware\ImageResize;

class DefaultMutator extends MutatorAbstract
{
    protected static $regexp = "/(?<original>[^-]+)-(?<size>(?<width>\d*)x(?<height>\d*))-?(?<signature>[0-9a-z]*)/";

    public function execute()
    {
        /* Crop or resize. */
        extract($this->options);
        if (null !== $width && null !== $height) {
            $this->image->grab($width, $height);
        } else {
            $this->image->resize($width, $height, true);
        }

        return $this;
    }
}
