<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'log_conf.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Ошибка подключения к базе данных."]);
    exit();
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$product_id) {
    echo json_encode(["success" => false, "message" => "Не указан ID продукта."]);
    exit();
}

$stmt = $conn->prepare("SELECT author, review_text, created_at FROM reviews WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode(["success" => true, "reviews" => $reviews]);
?>