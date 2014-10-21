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

class ScaleAndCropMutator extends MutatorAbstract
{
    protected static $regexp =
        "/(?<original>[^-]+)-(?<size>(?<scale>\d*)-(?<width>\d*)x(?<height>\d*))-?(?<signature>[0-9a-z]*)/";

    public function execute()
    {
        extract($this->options);

        /* Scale with the given percentage... */
        $scaled_width  = round($scale / 100 * $this->image->width());
        $scaled_height = round($scale / 100 * $this->image->height());
        $this->image->resize($scaled_width, $scaled_height);

        $width = $width ? $width : $scaled_width;
        $height = $height ? $height : $scaled_height;

        /* ... and crop. */
        $this->image->crop($width, $height);

        return $this;
    }
}
