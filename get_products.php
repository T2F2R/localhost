<?php
include 'config.php';

// Разрешение CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Content-Type: application/json');

// Обработка preflight-запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Проверка авторизации
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    echo json_encode(["success" => false, "message" => "Доступ запрещен."]);
    http_response_code(401);
    exit();
}

$token = $headers['Authorization'];
$validToken = 'your_secret_token'; // Пример токена (должен быть проверен в БД или JWT)
if ($token !== $validToken) {
    echo json_encode(["success" => false, "message" => "Недействительный токен."]);
    http_response_code(401);
    exit();
}

// Если авторизация успешна
$db = new Database();
$sql = "SELECT * FROM Products";
$products = $db->query($sql);

echo json_encode($products, JSON_UNESCAPED_UNICODE);
?>
