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

namespace Slim\Middleware;

use Intervention\Image\Image;
use Slim\Middleware\ImageResize\DefaultMutator;

class ImageResize extends \Slim\Middleware
{

    public $options;
    private $mutator;

    public function __construct($options = null)
    {

        /* Default options. */
        $this->options = array(
            "extensions" => array("jpg", "jpeg", "png", "gif"),
            "cache" => "cache",
            "regexp" => "/(?<original>[^-]+)-(?<size>(?<width>\d*)x(?<height>\d*))-?(?<signature>[0-9a-z]*)/",
            "quality" => 90,
            "sizes" => null,
            "secret" => null
        );

        if ($options) {
            $this->options = array_merge($this->options, (array)$options);
        }
    }

    public function parse($target)
    {
        $pathinfo = pathinfo($target);
        if (preg_match($this->options["regexp"], $pathinfo["filename"], $matches)) {
            foreach ($matches as $key => $value) {
                if (empty($value)) {
                    $matches[$key] = null;
                }
                if (is_numeric($key)) {
                    unset($matches[$key]);
                }
            }

            /* TODO: Make these pretty. */
            $extra["cache"] = $_SERVER["DOCUMENT_ROOT"] . "/" .
                    $this->options["cache"] . "/" . $target;

            $extra["source"] = $_SERVER["DOCUMENT_ROOT"] . "/" . $pathinfo["dirname"] . "/" .
                                 $matches["original"] . "." . $pathinfo["extension"];

            return array_merge($matches, $pathinfo, $extra);
        }
        return false;
    }

    public function call()
    {
        $request  = $this->app->request;
        $response = $this->app->response;

        $target   = $request->getResourceUri();

        if ($matched = $this->parse($target)) {
            extract($matched);
        };

        /* Extract array variables to current symbol table */
        if ($matched && $this->allowed(["extension" => $extension, "size" => $size, "signature" => $signature])) {

            $this->mutator = new DefaultMutator($matched);
            $this->mutator->execute();

            /* When requested save image to cache folder. */
            if ($this->options["cache"]) {
                $dir = pathinfo($cache, PATHINFO_DIRNAME);
                if (false === is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $this->mutator->save();
            }

            $response->header("Content-type", $this->mutator->mime());
            $response->body($this->mutator->encode());

        } else {
            $this->next->call();
        }
    }

    public function allowed($parameters = array())
    {
        extract($parameters);
        return $this->allowedExtension($extension) &&
               $this->allowedSize($size) &&
               $this->validSignature($parameters);
    }

    public function allowedExtension($extension = null)
    {
        return $extension && in_array($extension, $this->options["extensions"]);
    }

    public function allowedSize($size = null)
    {
        if (false == !!$this->options["sizes"]) {
            /* All sizes are allowed. */
            return true;
        } else {
            /* Only sizes passed in as array are allowed. */
            return is_array($this->options["sizes"]) && in_array($size, $this->options["sizes"]);
        }
    }

    public function validSignature($parameters = null)
    {
        /* Default arguments. */
        $arguments = array(
            "size" => null,
            "signature" => null
        );

        if ($parameters) {
            $arguments = array_merge($arguments, (array)$parameters);
        }

        if (false == !!$this->options["secret"] && null === $arguments["signature"]) {
            /* No secret is set or passed. All shall pass. */
            return true;
        } else {
            $signature = self::signature(array(
                "size" => $arguments["size"],
                "secret" => $this->options["secret"]
            ));

            return $arguments["signature"] === $signature;
        }
    }

    public static function signature($parameters = null)
    {
        /* Default arguments. */
        $arguments = array(
            "size" => null,
            "secret" => null,
            "width" => null,
            "height" => null
        );

        if ($parameters) {
            $arguments = array_merge($arguments, (array)$parameters);
        }

        $sha1 = sha1("{$arguments["size"]}:{$arguments["secret"]}");

        /* We use only 16 first characters. Secure enough. */
        return substr($sha1, 0, 16);
    }
}
