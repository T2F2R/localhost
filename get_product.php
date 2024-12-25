<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require_once 'log_conf.php';

$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connection->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка подключения к базе данных'], JSON_UNESCAPED_UNICODE);
    exit();
}

$productId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($productId) {
    $stmt = $connection->prepare("SELECT * FROM Products WHERE product_id = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Ошибка подготовки запроса'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Товар не найден'], JSON_UNESCAPED_UNICODE);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Отсутствует ID продукта'], JSON_UNESCAPED_UNICODE);
}

$connection->close();
?>
