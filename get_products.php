<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

session_start();

require_once 'log_conf.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Ошибка подключения к базе данных."]);
    exit();
}

$authHeader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

if (!$authHeader) {
    echo json_encode(["success" => false, "message" => "Токен отсутствует"]);
    exit;
}

list($bearer, $token) = explode(' ', $authHeader);

if ($bearer !== 'Bearer' || !$token) {
    echo json_encode(["success" => false, "message" => "Неверный формат токена"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("SELECT * FROM products");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode(["success" => true, "products" => $products]);
    } else {
        echo json_encode(["success" => false, "message" => "Продукты не найдены"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Токен не найден"]);
}

$stmt->close();
?>
