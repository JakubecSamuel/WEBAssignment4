<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
date_default_timezone_set("Europe/Bratislava");

$servername = "localhost";
$username = "INSERT_NAME";
$password = "INSERT_PASSWORD";
$dbname = "pocasie";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currentHour = date('G');

if ($currentHour >= 6 && $currentHour < 15) {
    $timeRange = "6:00-15:00";
} elseif ($currentHour >= 15 && $currentHour < 21) {
    $timeRange = "15:00-21:00";
} elseif ($currentHour >= 21 && $currentHour < 0) {
    $timeRange = "21:00-24:00";
} else {
    $timeRange = "24:00-6:00";
}

$sql = "UPDATE cas SET counter = counter + 1 WHERE time_range = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $timeRange);
$stmt->execute();

$stmt->close();
$conn->close();
?>
