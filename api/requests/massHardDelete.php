<?php

require_once '../includes/requestHeaders.php';
require_once '../includes/classes.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['productIds']) || !is_array($input['productIds'])) {
    $response->setSuccess(false);
    $response->setError('Invalid input. Expected an array of product IDs.');
    
    echo json_encode($response);
    exit();
}

$massDeleteResponse = Product::massHardDelete($input['productIds']);

echo $massDeleteResponse;


?>