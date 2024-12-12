<?php
include 'config.php';

header("Access-Control-Allow-Origin: *");

$db = new Database();

$sql = "SELECT * FROM Products";
$products = $db->query($sql);

header('Content-Type: application/json');
echo json_encode($products, JSON_UNESCAPED_UNICODE);
?>