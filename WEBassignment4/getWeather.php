<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');


$apiKey = "e97c91139a5e6c52a2d0ec6e59195766";
$city = htmlspecialchars($_GET['city']);
$apiUrl = "http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric";

$weatherData = file_get_contents($apiUrl);
echo $weatherData;

$servername = "localhost";
$username = "INSERT_NAME";
$password = "INSERT_PASSWORD";
$dbname = "pocasie";
$tableName = "pocasie";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$weatherDataArray = json_decode($weatherData, true);

if (isset($weatherDataArray["sys"]["country"])) {
    $countryCode = $weatherDataArray["sys"]["country"];
    $countryFlag = "https://restcountries.com/data/{$countryCode}.svg";

    // Check if the country exists in the database
    $sql = "SELECT * FROM {$tableName} WHERE country_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $countryCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the country exists, update the visits count
        $sql = "UPDATE {$tableName} SET visit_counter = visit_counter + 1 WHERE country_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $countryCode);
        $stmt->execute();
    } else {
        // If the country doesn't exist, insert a new record
        $sql = "INSERT INTO {$tableName} (country_name, flag_img, visit_counter) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $countryCode, $countryFlag);
        $stmt->execute();
    }

    $stmt->close();
}

$conn->close();
?>