<?php

class Database
{
    private $host = 'localhost';
    private $db_name = 'db_edificio_v6';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }

    // Métodos para obtener valores de configuración
    public function getHost()
    {
        return $this->host;
    }

    public function getDbName()
    {
        return $this->db_name;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    // Método estático para obtener la IP del servidor
    public static function getServerIP()
    {
        // Obtener IP local del servidor
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = shell_exec('ipconfig');
            preg_match('/IPv4[^:]*:\s*([0-9\.]+)/', $output, $matches);
            return $matches[1] ?? '127.0.0.1';
        } else {
            return trim(shell_exec("hostname -I | awk '{print $1}'")) ?: '127.0.0.1';
        }
    }
}
