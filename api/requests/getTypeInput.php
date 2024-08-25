<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../database.php';
require_once '../Product.php';
require_once '../models/DVD.php';
require_once '../models/Book.php';
require_once '../models/Furniture.php';


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
