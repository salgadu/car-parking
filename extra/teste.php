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

    $sql = file_get_contents(__DIR__ . '../teste.sql');
    $pdo->exec($sql);
    echo '[OK] Registros inseridos com sucesso' . PHP_EOL;
} catch (PDOException $exception) {
    echo '[ERROR] ' . $exception->getMessage() . PHP_EOL;
}
