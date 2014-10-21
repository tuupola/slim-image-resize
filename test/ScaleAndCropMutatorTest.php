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

namespace Test;

use Slim\Middleware\ImageResize;
use Slim\Middleware\ImageResize\ScaleAndCropMutator;

class ScaleAndCropMutatorTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $_SERVER["DOCUMENT_ROOT"] = __DIR__ . "/../example/";
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldParseDimensions()
    {
        $mutator = new ScaleAndCropMutator();
        $parsed = $mutator->parse("images/viper-70-400x200.jpg");
        $this->assertEquals($parsed["filename"], "viper-70-400x200");
        $this->assertEquals($parsed["basename"], "viper-70-400x200.jpg");
        $this->assertEquals($parsed["extension"], "jpg");
        $this->assertEquals($parsed["dirname"], "images");
        $this->assertEquals($parsed["original"], "viper");
        $this->assertEquals($parsed["size"], "70-400x200");
        $this->assertEquals($parsed["width"], "400");
        $this->assertEquals($parsed["height"], "200");
        $this->assertEquals($parsed["scale"], "70");
        //$this->assertEquals($parsed["source"], "/var/www/www.example.com/public/images/viper.jpg");
        //$this->assertEquals($parsed["cache"], "/var/www/www.example.com/public/cache/images/viper-400x200.jpg");
        $this->assertNull($parsed["signature"]);
    }

    public function testParseShouldReturnFalse()
    {
        $middleware = new ImageResize();
        $parsed = $middleware->mutator->parse("images/viper-new.jpg");
        $this->assertFalse($parsed);
    }

    public function testParseShouldReturnMime()
    {
        $mutator = new ScaleAndCropMutator();
        $parsed = $mutator->parse("images/viper-70-400x200.jpg");
        $this->assertEquals($mutator->mime(), "image/jpeg");
    }

    public function testExecuteShouldReturnSelf()
    {
        $mutator = new ScaleAndCropMutator();
        $parsed = $mutator->parse("images/viper-70-400x200.jpg");
        $this->assertInstanceOf("Slim\Middleware\ImageResize\ScaleAndCropMutator", $mutator->execute());
    }

    public function testExecuteShouldFit()
    {
        $mutator = new ScaleAndCropMutator();
        $parsed = $mutator->parse("images/viper-70-400x200.jpg");
        $mutator->execute();
        $this->assertEquals($mutator->image->width(), 400);
        $this->assertEquals($mutator->image->height(), 200);
    }

    public function testExecuteShouldResize()
    {
        $mutator = new ScaleAndCropMutator();
        $parsed = $mutator->parse("images/viper-70-400x.jpg");
        $mutator->execute();
        $this->assertEquals($mutator->image->width(), 400);
        $this->assertEquals($mutator->image->height(), 402);

        $parsed = $mutator->parse("images/viper-70-x200.jpg");
        $mutator->execute();
        $this->assertEquals($mutator->image->width(), 536);
        $this->assertEquals($mutator->image->height(), 200);
    }
}
