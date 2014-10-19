<?php

namespace Slim\Middleware\ImageResize;

use Intervention\Image\Image;

class DefaultMutator implements MutatorInterface
{
    private $options;
    public $image;

    public function __construct($options = array())
    {

        /* Default options. */
        $this->options = array(
        );

        if ($options) {
            $this->options = array_merge($this->options, $options);
        }

        extract($this->options);
        $this->image = Image::make($source);
    }

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

    public function save($quality = 90)
    {
        extract($this->options);
        return $this->image->save($cache, $quality);
    }

    public function mime()
    {
        return $this->image->mime;
    }

    public function encode()
    {
        return $this->image->encode();
    }
}
