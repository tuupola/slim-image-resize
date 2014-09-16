<?php

namespace Slim\Middleware;
use Intervention\Image\Image;

/**
 * Provides automagical image resizing.
 *
 * @package    Slim
 * @author     Mika Tuupola <tuupola@appelsiini.net>
 */
class ImageResize extends \Slim\Middleware {

    public $options;

    public function __construct($options = null) {

        /* Default options. */
        $this->options = array(
            "extensions" => array("jpg", "jpeg", "png", "gif"),
            "cache" => "cache",
            "quality" => 90
        );

        if ($options) {
            $this->options = array_merge($this->options, (array)$options);
        }
    }

    public function call() {
        $request  = $this->app->request;
        $response = $this->app->response;

        $target   = $request->getResourceUri();
        $pathinfo = pathinfo($target);

        $cache    = $_SERVER["DOCUMENT_ROOT"] . "/" .
                    $this->options["cache"] . $target;

        if ($this->shouldResize($pathinfo)) {
            preg_match("/([^-]+)-(\d*)x(\d*)/", $pathinfo["filename"], $matches);

            $source = $_SERVER["DOCUMENT_ROOT"] . $pathinfo["dirname"] . "/" .
                      $matches[1] . "." . $pathinfo["extension"];

            $width  = $matches[2] ? $matches[2] : null;
            $height = $matches[3] ? $matches[3] : null;

            $image = Image::make($source);

            /* Crop or resize. */
            if (null !== $width && null !== $height) {
                $image->grab($width, $height);
            } else {
                $image->resize($width, $height, true);
            }

            /* When requested save image to cache folder. */
            if ($this->options["cache"]) {
                $dir = pathinfo($cache, PATHINFO_DIRNAME);
                if (false === is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $image->save($cache, $this->options["quality"]);
            }

            $response->header("Content-type", $image->mime);
            $response->body($image->encode());
        } else {
            $this->next->call();
        }
    }

    private function shouldResize($pathinfo) {
        return isset($pathinfo["extension"]) &&
               in_array($pathinfo["extension"], $this->options["extensions"]);
    }
}
