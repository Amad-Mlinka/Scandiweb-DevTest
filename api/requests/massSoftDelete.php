<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once 'database.php';
require_once 'Product.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['productIds']) || !is_array($input['productIds'])) {
        throw new Exception("Invalid input. Expected an array of product IDs.");
    }

    Product::massSoftDelete($input['productIds']);

    echo json_encode(['status' => 'success', 'message' => 'Products deleted successfully']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>