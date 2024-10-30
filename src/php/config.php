<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'site';
    private $user = 'shop';
    private $password = '1234';
    private $pdo;

    // Конструктор для подключения к базе данных
    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения: " . $e->getMessage());
        }
    }

    // Метод для выполнения запроса и получения данных(SELECT)
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Ошибка выполнения запроса: " . $e->getMessage());
        }
    }
    
    // Метод для выполнения команд, не возвращающих данные (INSERT, UPDATE, DELETE)
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            die("Ошибка выполнения команды: " . $e->getMessage());
        }
    }
}
?>