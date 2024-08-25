<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
require_once 'database.php';
require_once 'Product.php';
require_once 'models/DVD.php';
require_once 'models/Book.php';
require_once 'models/Furniture.php';

$products = Product::fetchAll();


header('Content-Type: application/json');
echo $products;

?>
