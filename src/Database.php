<?php
namespace Dell7420\Academy;
class Database {

    private $host = "localhost";
    private $user = "root";
    private $password = "root123";
    private $database = "academy";

    public function connect() {
        $conn = new \mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->database
        );

        if ($conn->connect_error) {
            die("Database Connection Failed: " . $conn->connect_error);
        }

        $conn->set_charset("utf8mb4");

        return $conn;
    }
}