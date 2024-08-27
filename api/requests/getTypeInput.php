<?php

require_once '../includes/requestHeaders.php';
require_once '../includes/classes.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
$response = new Response();

if (!isset($_GET['typeId']) || !is_numeric($_GET['typeId'])) {
    $response->setSuccess(false);
    $response->setError('Invalid input. Expected a typeId.');
    echo json_encode($response);
    exit();
}

$typeId = (int) $_GET['typeId'];

$response = Product::getHtmlForType($typeId);

echo $response;


?>
