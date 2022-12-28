<?php

class DB {
    private $host = 'localhost';
    private $user = 'root';
    private $password = 'admin';
    private $database = 'car_parking';

    public function connect() {
        $mysql_connect_str = "mysql:host=$this->host;dbname=$this->database";
        $dbConnection = new PDO($mysql_connect_str, $this->user, $this->password);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
    }
}
