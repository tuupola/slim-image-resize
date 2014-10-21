<?php

namespace Test;

use Slim\Middleware\ImageResize;
use Slim\Middleware\ImageResize\DefaultMutator;

class DefaultMutatorTest extends \PHPUnit_Framework_TestCase
{

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldParseDimensions()
    {
        $_SERVER["DOCUMENT_ROOT"] = "/var/www/www.example.com/public";

        $mutator = new DefaultMutator();
        $parsed = $mutator->parse("images/viper-400x200.jpg");
        $this->assertEquals($parsed["filename"], "viper-400x200");
        $this->assertEquals($parsed["basename"], "viper-400x200.jpg");
        $this->assertEquals($parsed["extension"], "jpg");
        $this->assertEquals($parsed["dirname"], "images");
        $this->assertEquals($parsed["original"], "viper");
        $this->assertEquals($parsed["size"], "400x200");
        $this->assertEquals($parsed["width"], "400");
        $this->assertEquals($parsed["height"], "200");
        $this->assertEquals($parsed["source"], "/var/www/www.example.com/public/images/viper.jpg");
        //$this->assertEquals($parsed["cache"], "/var/www/www.example.com/public/cache/images/viper-400x200.jpg");
        $this->assertNull($parsed["signature"]);
    }

    public function testParseShouldReturnFalse()
    {
        $middleware = new ImageResize();
        $parsed = $middleware->mutator->parse("images/viper-new.jpg");
        $this->assertFalse($parsed);
    }
}
