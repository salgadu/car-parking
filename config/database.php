<?php

// require __DIR__ . "/../vendor/autoload.php";
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
// $dotenv->load();

class DB {
    private $host = 'localhost';
    private $user = 'root';
    private $password = 'admin';
    private $database = 'car_parking';

    // public function __construct() {
    //     $this->host = getenv('DB_HOST');
    //     $this->user = getenv('DB_USER');
    //     $this->password = getenv('DB_PASSWORD');
    //     $this->database = getenv('DB_NAME');
    // }

    public function connect() {
        $mysql_connect_str = "mysql:host=$this->host;dbname=$this->database";
        $dbConnection = new PDO($mysql_connect_str, $this->user, $this->password);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
    }
}
