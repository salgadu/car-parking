<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();


// $app->group("/cars", function () use ($app) {
//     $app->get("/all", "App\Controller\Car:getAll");
//     $app->get("/{id}", "App\Controller\Car:get");
//     $app->post("/add", "App\Controller\Car:add");
//     $app->put("/update/{id}", "App\Controller\Car:update");
//     $app->delete("/delete/{id}", "App\Controller\Car:delete");
// });

// Obter todos os carros
$app->get("/cars/all", function (
    Request $request,
    Response $response,
    array $args
) {
    $sql = "SELECT * FROM tbl_car";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->query($sql);
        $cars = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        return $response->withJson($cars, 200);
    } catch (PDOException $e) {
        $error = [
            "status" => "failed",
            "message" => $e->getMessage(),
        ];
        return $response->withJson($error, 500);
    }
});

// Obter um unico carro pelo id
$app->get("/cars/{id}", function (
    Request $request,
    Response $response,
    array $args
) {
    $id = $args["id"];
    $sql = "SELECT * FROM tbl_car WHERE id = $id";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->query($sql);
        $car = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        return $response->withJson($car, 200);
    } catch (PDOException $e) {
        $error = [
            "status" => "failed",
            "message" => $e->getMessage(),
        ];
        return $response->withJson($error, 500);
    }
});

// Adicionar um carro
$app->post("/cars/add", function (
    Request $request,
    Response $response,
    array $args
) {
    $data = $request->getParsedBody();
    $sql =
        "INSERT INTO tbl_car (brand_model, license_plate, tbl_owner_id) VALUES (:brand_model, :license_plate, :tbl_owner_id)";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":brand_model", $data["brand_model"]);
        $stmt->bindParam(":license_plate", $data["license_plate"]);
        $stmt->bindParam(":tbl_owner_id", $data["tbl_owner_id"]);
        $stmt->execute();

        $data["id"] = $conn->lastInsertId();
        $db = null;

        return $response->withJson($data, 200);
    } catch (PDOException $e) {
        $error = [
            "status" => "failed",
            "message" => $e->getMessage(),
        ];
        return $response->withJson($error, 500);
    }
});

// Atualizar um carro
$app->put("/cars/update/{id}", function (
    Request $request,
    Response $response,
    array $args
) {
    $id = $args["id"];
    $data = $request->getParsedBody();
    $sql = "UPDATE tbl_car SET brand_model = :brand_model, license_plate = :license_plate, tbl_owner_id = :tbl_owner_id WHERE id = $id";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":brand_model", $data["brand_model"]);
        $stmt->bindParam(":license_plate", $data["license_plate"]);
        $stmt->bindParam(":tbl_owner_id", $data["tbl_owner_id"]);
        $stmt->execute();

        $db = null;

        return $response->withJson($data, 200);
    } catch (PDOException $e) {
        $error = [
            "status" => "failed",
            "message" => $e->getMessage(),
        ];
        return $response->withJson($error, 500);
    }
});

// Deletar um carro
$app->delete("/cars/delete/{id}", function (
    Request $request,
    Response $response,
    array $args
) {
    $id = $args["id"];
    $sql = "DELETE FROM tbl_car WHERE id = $id";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $db = null;

        return $response->withJson("Carro deletado com sucesso", 200);
    } catch (PDOException $e) {
        $error = [
            "status" => "failed",
            "message" => $e->getMessage(),
        ];
        return $response->withJson($error, 500);
    }
});
