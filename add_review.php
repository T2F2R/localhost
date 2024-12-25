<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once 'log_conf.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Ошибка подключения к базе данных."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$product_id = $data['product_id'] ?? null;
$author = $data['author'] ?? null;
$review_text = $data['review_text'] ?? null;

if (!$product_id || !$author || !$review_text) {
    echo json_encode(["success" => false, "message" => "Не хватает данных для добавления отзыва."]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO reviews (product_id, author, review_text) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $product_id, $author, $review_text);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Отзыв добавлен."]);
} else {
    echo json_encode(["success" => false, "message" => "Ошибка добавления отзыва."]);
}
?>