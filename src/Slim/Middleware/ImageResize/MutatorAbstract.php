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

use Intervention\Image\ImageManagerStatic as Image;

abstract class MutatorAbstract implements MutatorInterface
{

    protected $options = array("quality" => 90); /* Set defaults here. */
    protected $image;

    public function __construct($options = array())
    {
        self::options($options);
    }

    public function options($options = array())
    {
        if ($options) {
            $this->options = array_merge($this->options, $options);
        }

        if (isset($this->options["source"])) {
            $this->image = Image::make($this->options["source"]);
        }
    }

    public static function regexp()
    {
        return static::$regexp;
    }

    public function parse($target)
    {
        $pathinfo = pathinfo($target);
        if (preg_match(self::regexp(), $pathinfo["filename"], $matches)) {
            foreach ($matches as $key => $value) {
                if (empty($value)) {
                    $matches[$key] = null;
                }
                if (is_numeric($key)) {
                    unset($matches[$key]);
                }
            }

            $extra["source"] = $_SERVER["DOCUMENT_ROOT"] . "/" . $pathinfo["dirname"] . "/" .
                               $matches["original"] . "." . $pathinfo["extension"];

            return array_merge($matches, $pathinfo, $extra);
        }
        return false;
    }

    public function save($file)
    {
        return $this->image->save($file, $this->options["quality"]);
    }

    public function mime()
    {
        return $this->image->mime;
    }

    public function encode()
    {
        return $this->image->encode();
    }

    abstract public function execute();
}
