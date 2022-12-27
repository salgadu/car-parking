<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . "/../vendor/autoload.php";

$app = AppFactory::create();

// routes

$app->get("/", "App\Controller\Home:getHelp");
$app->get("/status", "App\Controller\Home:getStatus");


