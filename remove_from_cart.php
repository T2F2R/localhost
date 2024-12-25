<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'log_conf.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id'])) {
    echo json_encode(["success" => false, "message" => "Некорректные данные"]);
    exit();
}

$product_id = $data['product_id'];

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    echo json_encode(["success" => false, "message" => "Нет токена авторизации"]);
    exit();
}

$auth_token = str_replace("Bearer ", "", $headers['Authorization']);
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Проверьте, совпадает ли поле в SQL с вашим
$stmt = $conn->prepare("SELECT user_id FROM users WHERE token = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Ошибка подготовки запроса: " . $conn->error]);
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

// Удаление товара из корзины
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Товар успешно удалён"]);
} else {
    echo json_encode(["success" => false, "message" => "Ошибка при удалении товара"]);
}

$stmt->close();
$conn->close();