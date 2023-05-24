<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

$url = "https://restcountries.com/v3.1/all?fields=name;alpha2Code;flag";
$response = file_get_contents($url);
echo $response;
?>
