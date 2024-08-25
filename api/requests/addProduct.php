<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../database.php';
require_once '../Product.php';
require_once '../models/DVD.php';
require_once '../models/Book.php';
require_once '../models/Furniture.php';
require_once '../models/Response.php'; 

$data = json_decode(file_get_contents('php://input'), true);
$response = new Response();

if (json_last_error() !== JSON_ERROR_NONE) {
    $response->setSuccess(false);
    $response->setMessage('Invalid JSON input');
    $response->setError('Error parsing JSON input');
    echo json_encode($response);
    exit();
}

if (!isset($data['sku'], $data['name'], $data['price'], $data['type'], $data['attributeName'], $data['attributeValue'])) {
    $response->setSuccess(false);
    $response->setMessage('Missing required fields');
    $response->setError('Please provide sku, name, price, type, attributeName, and attributeValue.');
    echo json_encode($response);
    exit();
}

try {

    $response = Product::saveToDatabase($data);   

} catch (Exception $e) {

    $response->setSuccess(false);
    $response->setMessage('Failed to create product');
    $response->setError($e->getMessage());
}

echo json_encode($response);
?>
