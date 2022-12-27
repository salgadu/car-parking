<?php

// Index Controller

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

class Home
{
    public function getHelp(Request $request, Response $response, array $args)
    {
        $help = [
            "status" => "success",
            "message" => "Welcome to Slim Framework 4",
            "routes" => [
                "/status" => "Check if the API is running",
                "/cars/all" => "Get all cars",
                "/cars/{id}" => "Get a car by id",
                "/cars/add" => "Add a new car",
                "/cars/update/{id}" => "Update a car by id",
                "/cars/delete/{id}" => "Delete a car by id",
            ],
        ];

        return $response->withJson($help, 200);
    }

    public function getStatus(Request $request, Response $response, array $args)
    {
        $status = [
            "status" => "success",
            "message" => "API is running",
        ];

        return $response->withJson($status, 200);
    }
}