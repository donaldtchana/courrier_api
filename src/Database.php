<?php
class Database
{
    private $conn;
    public function __construct(private string $host, private string $user, private string $password, private string $name,private string $port)
    {
    }

    public function connect(): PDO
    {
        $this->conn = null;
        $str = "mysql:host={$this->host};port={$this->port};dbname={$this->name};charset=utf8";
        try {
            $this->conn = new PDO($str, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);
        } catch (PDOException $e) {
            echo 'Connectio Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
