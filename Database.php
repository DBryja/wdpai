<?php
require_once "config.php";
class Database
{
    private $username;
    private $password;
    private $host;
    private $database;
    private $port;

    public function __construct()
    {
        $this->username = USERNAME;
        $this->password = PASSWORD;
        $this->host = HOST;
        $this->database = DATABASE;
        $this->port = PORT;
    }

    public function connect()
    {
        static $conn = null;
        if ($conn === null) {
            try {
                $conn = new PDO("pgsql:host=$this->host;port=$this->port;dbname=$this->database", $this->username, $this->password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return $conn;
    }
}