<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

// Login
$app->post("/signin", function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $email = $data['email'];
    $password = $data['password'];
  
    $db = new DB();
    $pdo = $db->connect();
  
    $sql = "SELECT * FROM tbl_user WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);
  
    if (!$user) {
      $response->getBody()->write(json_encode(array(
        "status" => 404,
        "message" => "User not found"
      )));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
  
    if (!password_verify($password, $user->password)) {
      $response->getBody()->write(json_encode(array(
        "status" => 401,
        "message" => "Wrong password"
      )));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
  
    $response->getBody()->write(json_encode(array(
      "status" => 200,
      "message" => "Sign in successfully",
      "data" => $user
    )));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
  });

// Cadastro
$app->post("/signup", function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $email = $data['email'];
    $password = $data['password'];
    $name = $data['name'];
  
    $db = new DB();
    $pdo = $db->connect();
  
    $sql = "SELECT * FROM tbl_user WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);
  
    if ($user) {
      $response->getBody()->write(json_encode(array(
        "status" => 409,
        "message" => "Email already exists"
      )));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
    }
  
    $sql = "INSERT INTO tbl_user (email, password, name) VALUES (:email, :password, :name)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
    $stmt->bindParam(':name', $name);
   
    $stmt->execute();
  
    $response->getBody()->write(json_encode(array(
      "status" => 201,
      "message" => "Sign up successfully"
    )));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
  });
  


  