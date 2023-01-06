<?php

declare(strict_types=1);

try {
    $host = 'localhost';
    $name = 'car_parking';
    $user = 'root';
    $pass = 'admin';
    $port = 3306;

    $pdo = new PDO("mysql:host=${host};port=${port};charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("DROP DATABASE IF EXISTS ${name}");
    echo '[OK] Banco de dados excluído com sucesso' . PHP_EOL;

    $pdo->exec("CREATE DATABASE ${name}");
    echo '[OK] Banco de dados criado com sucesso' . PHP_EOL;

    $pdo->exec("USE ${name}");
    echo '[OK] Banco de dados selecionado com sucesso' . PHP_EOL;

    $sql = file_get_contents(__DIR__ . '../database.sql');
    $pdo->exec($sql);
    echo '[OK] Registros inseridos com sucesso' . PHP_EOL;
} catch (PDOException $exception) {
    echo '[ERROR] ' . $exception->getMessage() . PHP_EOL;
}
