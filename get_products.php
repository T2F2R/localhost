<?php
include 'config.php';

$sql = "SELECT * FROM Products";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($products, JSON_UNESCAPED_UNICODE);
?>