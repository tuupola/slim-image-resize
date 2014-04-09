<?php

require "../vendor/autoload.php";

$app = new \Slim\Slim();
$app->add(new Slim\Middleware\ImageResize());

$app->get("/", function() use ($app) {
    $app->render("index.html", array(
        "app" => $app
    ));
});

$app->run();