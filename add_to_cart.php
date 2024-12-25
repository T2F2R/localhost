<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'log_conf.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode(["success" => false, "message" => "Некорректные данные"]);
    exit();
}

$product_id = $data['product_id'];
$quantity = $data['quantity'];

// Проверка авторизации пользователя
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    echo json_encode(["success" => false, "message" => "Нет токена авторизации"]);
    exit();
}

// Удаление префикса "Bearer" из токена
$auth_token = $headers['Authorization'];
if (str_starts_with($auth_token, 'Bearer ')) {
    $auth_token = substr($auth_token, 7);
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Ошибка подключения к базе данных."]);
    exit();
}

// Получение user_id из токена
$stmt = $conn->prepare("SELECT user_id FROM users WHERE token = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Ошибка в запросе: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $auth_token);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Пользователь не авторизован"]);
    exit();
}

$user = $result->fetch_assoc();
$user_id = $user['user_id'];

// Добавление в корзину
$stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Ошибка в запросе: " . $conn->error]);
    exit();
}

$stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Товар добавлен в корзину"]);
} else {
    echo json_encode(["success" => false, "message" => "Ошибка добавления в корзину"]);
}

$stmt->close();
$conn->close();
?>