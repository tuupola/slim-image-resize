<?php

namespace Slim\Middleware;

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
            "foo" => "bar"
        );

        if ($options) {
            $this->options = array_merge($this->options, (array)$options);
        }
    }

    public function call() {
        $request = $this->app->request;
        $this->next->call();
    }
}
