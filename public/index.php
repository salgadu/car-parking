<?php



use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../config/database.php";

$app = AppFactory::create();

require __DIR__ . '/../config/routes.php';
require __DIR__ . "/../config/middleware.php";

$app->run();
