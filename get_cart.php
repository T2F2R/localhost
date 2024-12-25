<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'log_conf.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Ошибка подключения к базе данных."]);
    exit();
}

$headers = getallheaders();
$authToken = $headers['Authorization'] ?? '';

if (!$authToken) {
    echo json_encode(["success" => false, "message" => "Токен авторизации отсутствует."]);
    exit();
}

if (str_starts_with($authToken, 'Bearer ')) {
    $authToken = substr($authToken, 7);
}

$stmt = $conn->prepare("SELECT user_id FROM users WHERE token = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Ошибка в запросе: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $authToken);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Некорректный токен авторизации."]);
    exit();
}

$user = $result->fetch_assoc();
$user_id = $user['user_id'];

$stmt->close();

$stmt = $conn->prepare("SELECT products.product_id, products.name, products.price, products.image_url 
    FROM cart
    INNER JOIN products ON cart.product_id = products.product_id
    WHERE cart.user_id = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Ошибка в запросе: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(["success" => true, "products" => $products]);
exit();
?>