<?php
require_once 'log_conf.php';

$password = 'user'; // Пароль пользователя
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$sql = "INSERT INTO users (username, password) VALUES ('user', '$hashed_password')";
if ($conn->query($sql) === TRUE) {
    echo "Пользователь успешно добавлен.";
} else {
    echo "Ошибка: " . $conn->error;
}

$conn->close();
?>