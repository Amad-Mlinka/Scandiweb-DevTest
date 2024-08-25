<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once '../database.php';
require_once '../Product.php';


$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['productIds']) || !is_array($input['productIds'])) {
    throw new Exception("Invalid input. Expected an array of product IDs.");
}

$massDeleteResponse = Product::massHardDelete($input['productIds']);

header('Content-Type: application/json');
echo $massDeleteResponse;


?>