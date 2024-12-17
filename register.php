<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'log_conf.php';

// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['username']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "message" => "Данные для регистрации отсутствуют."]);
    exit();
}

$username = $data['username'];
$password = $data['password'];

// Подключение к базе данных
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Ошибка подключения к базе данных."]);
    exit();
}

// Проверка существования пользователя
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Пользователь уже существует."]);
} else {
    // Хэширование пароля и добавление нового пользователя
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Регистрация успешна."]);
    } else {
        echo json_encode(["success" => false, "message" => "Ошибка при регистрации."]);
    }
}

$stmt->close();
$conn->close();
exit();
?>