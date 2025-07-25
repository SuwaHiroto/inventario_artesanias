<?php
class Conexion
{
    private $host = "if0_39558707_inventario_artesanias"; // Cambia el puerto si es necesario
    private $username = "if0_39558707_inventario_artesanias";
    private $password = "lc1VsiUnM0b8yU";
    private $dbname = "if0_39558707_inventario_artesanias";
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            throw new Exception("Error de conexión: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
