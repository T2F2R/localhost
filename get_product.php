<?php
include 'config.php';

header("Access-Control-Allow-Origin: *");

$db = new Database();

$productId = isset($_GET['id']) ? $_GET['id'] : null;

if ($productId) {
    $sql = "SELECT * FROM Products WHERE product_id = ?";
    $products = $db->query($sql, [$productId]); // Use parameterized query to prevent SQL injection

    if (count($products) > 0) {
        $product = $products[0]; // Get the single product
        header('Content-Type: application/json');
        echo json_encode($product, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404); // Product not found
        echo json_encode(['error' => 'Product not found'], JSON_UNESCAPED_UNICODE);
    }
} else {
    http_response_code(400); // Bad request - missing ID
    echo json_encode(['error' => 'Missing product ID'], JSON_UNESCAPED_UNICODE);
}
?>