<?php

require_once '../includes/requestHeaders.php';
require_once '../includes/classes.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$response = Product::getTypes();

echo $response;

?>
