<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'log_conf.php';

// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['username']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "message" => "Данные для авторизации отсутствуют."]);
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

// Проверка пользователя
$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $storedHash = $row['password'];

    if (password_verify($password, $storedHash)) {
        echo json_encode(["success" => true, "message" => "Авторизация успешна."]);
    } else {
        echo json_encode(["success" => false, "message" => "Неверный пароль."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Пользователь не найден."]);
}

$stmt->close();
$conn->close();
exit();
?>
