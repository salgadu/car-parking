<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Credentials: true");

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->get('/', function ($request, $response, $args) {
    return $response->write(file_get_contents(__DIR__ . '/../pages/routes.html'));
});

$app->group("/api/v1", function ($app) {
    $app->group("/user", function ($app) {
        // Acessing user authentication
        $app->group("/auth", function ($app) {
            $app->post("/signin", function (
                Request $request,
                Response $response,
                array $args
            ) {
                $data = $request->getParsedBody();
                $email = $data["email"];
                $password = $data["password"];

                $db = new DB();
                $pdo = $db->connect();

                $sql = "SELECT * FROM tbl_user WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":email", $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_OBJ);

                if (!$user) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Usuário não encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                if (!password_verify($password, $user->password)) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 401,
                            "message" => "Senha incorreta",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(401);
                }

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Usuário autenticado com sucesso",
                        "data" => $user,
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            });

            $app->post("/signup", function (
                Request $request,
                Response $response,
                array $args
            ) {
                $data = $request->getParsedBody();
                $email = $data["email"];
                $password = $data["password"];
                $name = $data["name"];

                $db = new DB();
                $pdo = $db->connect();

                try {
                    $sql = "SELECT * FROM tbl_user WHERE email = :email";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":email", $email);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_OBJ);

                    if ($user) {
                        $response->getBody()->write(
                            json_encode([
                                "status" => 409,
                                "message" => "Usuário já cadastrado",
                            ])
                        );
                        return $response
                            ->withHeader("Content-Type", "application/json")
                            ->withStatus(409);
                    }

                    $sql =
                        "INSERT INTO tbl_user (email, password, name) VALUES (:email, :password, :name)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":email", $email);
                    $stmt->bindParam(
                        ":password",
                        password_hash($password, PASSWORD_DEFAULT)
                    );
                    $stmt->bindParam(":name", $name);

                    $stmt->execute();

                    $response->getBody()->write(
                        json_encode([
                            "status" => 201,
                            "message" => "Usuario cadastrado com sucesso",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(201);
                } catch (PDOException $e) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 500,
                            "message" => "Erro ao cadastrar usuário",
                            "error" => $e->getMessage(),
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(500);
                }
            });
        });

            // Acessing all users
            $app->get("/all", function (
                Request $request,
                Response $response,
                array $args
            ) {
                $db = new DB();
                $pdo = $db->connect();

                $sql = "SELECT * FROM tbl_user";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_OBJ);

                if (!$users) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Nenhum usuário encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Usuários encontrados",
                        "data" => $users,
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            });

            // Acessing a specific user
            $app->get("/{id}", function (
                Request $request,
                Response $response,
                array $args
            ) {
                $id = $args["id"];

                $db = new DB();
                $pdo = $db->connect();

                $sql = "SELECT * FROM tbl_user WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_OBJ);

                if (!$user) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Usuário não encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Usuário encontrado",
                        "data" => $user,
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            });

            // Adding a new user
            $app->post("/add", function (
                Request $request,
                Response $response,
                array $args
            ) {
                $data = $request->getParsedBody();
                $email = $data["email"];
                $password = $data["password"];
                $name = $data["name"];

                $db = new DB();
                $pdo = $db->connect();

                $sql = "SELECT * FROM tbl_user WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":email", $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_OBJ);

                if ($user) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 409,
                            "message" => "Usuário já cadastrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(409);
                }

                $sql =
                    "INSERT INTO tbl_user (email, password, name) VALUES (:email, :password, :name)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(
                    ":password",
                    password_hash($password, PASSWORD_DEFAULT)
                );
                $stmt->bindParam(":name", $name);

                $stmt->execute();

                $response->getBody()->write(
                    json_encode([
                        "status" => 201,
                        "message" => "Usuario cadastrado com sucesso",
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(201);
            });

            // Updating a user
            $app->put("/{id}", function (
                Request $request,
                Response $response,
                array $args
            ) {
                $id = $args["id"];
                $data = $request->getParsedBody();
                $email = $data["email"];
                $password = $data["password"];
                $name = $data["name"];

                $db = new DB();
                $pdo = $db->connect();

                $sql = "SELECT * FROM tbl_user WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_OBJ);

                if (!$user) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Usuário não encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $sql =
                    "UPDATE tbl_user SET email = :email, password = :password, name = :name WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(
                    ":password",
                    password_hash($password, PASSWORD_DEFAULT)
                );
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":id", $id);

                $stmt->execute();

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Usuário atualizado com sucesso",
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            });

            // Deleting a user
            $app->delete("/{id}", function (
                Request $request,
                Response $response,
                array $args
            ) {
                $id = $args["id"];

                $db = new DB();
                $pdo = $db->connect();

                $sql = "SELECT * FROM tbl_user WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $id);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_OBJ);

                if (!$user) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Usuário não encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $sql = "DELETE FROM tbl_user WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $id);

                $stmt->execute();

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Usuário deletado com sucesso",
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            });
    });

    // Group car
    $app->group("/car", function ($app) {
        // Get all cars
        $app->get("/all", function (
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

                if (!$cars) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Nenhum carro encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Carros encontrados com sucesso",
                        "data" => $cars,
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);

            } catch (PDOException $e) {
                $error = [
                    "status" => "failed",
                    "message" => $e->getMessage(),
                ];
                return $response->withJson($error, 500);
            }
        });

        // Get a car
        $app->get("/{id}", function (
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

                if (!$car) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Carro não encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Carro encontrado com sucesso",
                        "data" => $car,
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            } catch (PDOException $e) {
                $error = [
                    "status" => "failed",
                    "message" => $e->getMessage(),
                ];
                return $response->withJson($error, 500);
            }
        });

        // Adding a new car
        $app->post("/add", function (
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
                $stmt->bindParam(
                    ":license_plate",
                    $data["license_plate"]
                );
                $stmt->bindParam(
                    ":tbl_owner_id",
                    $data["tbl_owner_id"]
                );
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

        // Updating a car
        $app->put("/update/{id}", function (
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
                $stmt->bindParam(
                    ":license_plate",
                    $data["license_plate"]
                );
                $stmt->bindParam(
                    ":tbl_owner_id",
                    $data["tbl_owner_id"]
                );
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

        // Deleting a car
        $app->delete("/delete/{id}", function (
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

                return $response->withJson(
                    "Carro deletado com sucesso",
                    200
                );
            } catch (PDOException $e) {
                $error = [
                    "status" => "failed",
                    "message" => $e->getMessage(),
                ];
                return $response->withJson($error, 500);
            }
        });
    });

    $app->group("/owner", function ($app) {
        // Get all owners
        $app->get("/all", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $sql = "SELECT * FROM tbl_owner";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->query($sql);
                $owners = $stmt->fetchAll(PDO::FETCH_OBJ);
                $db = null;

                if (!$owners) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Nenhum proprietário encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Proprietários encontrados com sucesso",
                        "data" => $owners,
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            } catch (PDOException $e) {
                $error = [
                    "status" => "failed",
                    "message" => $e->getMessage(),
                ];
                return $response->withJson($error, 500);
            }
        });

        // Get an owner
        $app->get("/{id}", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $id = $args["id"];
            $sql = "SELECT * FROM tbl_owner WHERE id = $id";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->query($sql);
                $owner = $stmt->fetch(PDO::FETCH_OBJ);
                $db = null;

                if (!$owner) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Proprietário não encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Proprietário encontrado com sucesso",
                        "data" => $owner,
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            } catch (PDOException $e) {
                $error = [
                    "status" => "failed",
                    "message" => $e->getMessage(),
                ];
                return $response->withJson($error, 500);
            }
        });

        // Adding a new owner
        $app->post("/add", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $data = $request->getParsedBody();
            $sql =
                "INSERT INTO tbl_owner (name, telephone) VALUES (:name, :telephone)";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":name", $data["name"]);
                $stmt->bindParam(":phone", $data["phone"]);
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

        // Updating an owner
        $app->put("/update/{id}", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $id = $args["id"];
            $data = $request->getParsedBody();
            $sql = "UPDATE tbl_owner SET name = :name, telephone = :telephone WHERE id = $id";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":name", $data["name"]);
                $stmt->bindParam(":phone", $data["phone"]);
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

        // Deleting an owner
        $app->delete("/delete/{id}", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $id = $args["id"];
            $sql = "DELETE FROM tbl_owner WHERE id = $id";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $db = null;

                return $response->withJson(
                    "Dono deletado com sucesso",
                    200
                );
            } catch (PDOException $e) {
                $error = [
                    "status" => "failed",
                    "message" => $e->getMessage(),
                ];
                return $response->withJson($error, 500);
            }
        });
    });

    $app->group("/register", function ($app) {
        // Get all registers
        $app->get("/all", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $sql = "SELECT * FROM tbl_register";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->query($sql);
                $registers = $stmt->fetchAll(PDO::FETCH_OBJ);
                $db = null;

                if (!$registers) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Nenhum registro encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Registros encontrados com sucesso",
                        "data" => $registers,
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            } catch (PDOException $e) {
                $error = [
                    "status" => "failed",
                    "message" => $e->getMessage(),
                ];
                return $response->withJson($error, 500);
            }
        });

        // Get a register
        $app->get("/{id}", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $id = $args["id"];
            $sql = "SELECT * FROM tbl_register WHERE id = $id";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->query($sql);
                $register = $stmt->fetch(PDO::FETCH_OBJ);
                $db = null;

                if (!$register) {
                    $response->getBody()->write(
                        json_encode([
                            "status" => 404,
                            "message" => "Registro não encontrado",
                        ])
                    );
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->withStatus(404);
                }

                $response->getBody()->write(
                    json_encode([
                        "status" => 200,
                        "message" => "Registro encontrado com sucesso",
                        "data" => $register,
                    ])
                );
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(200);
            } catch (PDOException $e) {
                $error = [
                    "status" => "failed",
                    "message" => $e->getMessage(),
                ];
                return $response->withJson($error, 500);
            }
        });

        // Adding a new register
        $app->post("/add", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $data = $request->getParsedBody();
            $sql =
                "INSERT INTO tbl_register (date, entry_time, departure_time, tbl_user_id, tbl_car_id, tbl_car_owner_id) VALUES (:date, :entry_time, :departure_time, :tbl_user_id, :tbl_car_id, :tbl_car_owner_id)";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":date", $data["date"]);
                $stmt->bindParam(":entry_time", $data["entry_time"]);
                $stmt->bindParam(":departure_time", $data["departure_time"]);
                $stmt->bindParam(":tbl_user_id", $data["tbl_user_id"]);
                $stmt->bindParam(":tbl_car_id", $data["tbl_car_id"]);
                $stmt->bindParam(":tbl_car_owner_id", $data["tbl_car_owner_id"]);
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

        // Updating a register
        $app->put("/update/{id}", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $id = $args["id"];
            $data = $request->getParsedBody();
            $sql =
                "UPDATE tbl_register SET date = :date, entry_time = :entry_time, departure_time = :departure_time, tbl_user_id = :tbl_user_id, tbl_car_id = :tbl_car_id, tbl_car_owner_id = :tbl_car_owner_id WHERE id = $id";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":date", $data["date"]);
                $stmt->bindParam(":entry_time", $data["entry_time"]);
                $stmt->bindParam(":departure_time", $data["departure_time"]);
                $stmt->bindParam(":tbl_user_id", $data["tbl_user_id"]);
                $stmt->bindParam(":tbl_car_id", $data["tbl_car_id"]);
                $stmt->bindParam(":tbl_car_owner_id", $data["tbl_car_owner_id"]);
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

        // Deleting a register
        $app->delete("/delete/{id}", function (
            Request $request,
            Response $response,
            array $args
        ) {
            $id = $args["id"];
            $sql = "DELETE FROM tbl_register WHERE id = $id";

            try {
                $db = new DB();
                $conn = $db->connect();

                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $db = null;

                return $response->withJson(
                    "Registro deletado com sucesso",
                    200
                );
            } catch (PDOException $e) {
                $error = [
                    "status" => "failed",
                    "message" => $e->getMessage(),
                ];
                return $response->withJson($error, 500);
            }
        });
    });
});
