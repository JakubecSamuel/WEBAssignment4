<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

$servername = "localhost";
$username = "INSERT_NAME";
$password = "INSERT_PASSWORD";
$dbname = "pocasie";
$tableName = "pocasie";

$conn = new mysqli($hostname, $username, $password, $dbname);

$apiKey = "e97c91139a5e6c52a2d0ec6e59195766";
$latitude = htmlspecialchars($_GET['lat']);
$longitude = htmlspecialchars($_GET['lon']);
$apiUrl = "http://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$apiKey}&units=metric";

$weatherData = file_get_contents($apiUrl);
$weatherDataArray = json_decode($weatherData, true);

if (!empty($weatherDataArray['coord']['lat']) && !empty($weatherDataArray['coord']['lon'])) {
    $radius = 50;
    $closestCityData = file_get_contents("http://api.openweathermap.org/data/2.5/find?lat={$latitude}&lon={$longitude}&cnt=1&units=metric&radius={$radius}&appid={$apiKey}");
    $closestCityDataArray = json_decode($closestCityData, true);
    if (!empty($closestCityDataArray['list'])) {
        $weatherDataArray = $closestCityDataArray['list'][0];
        $weatherData = json_encode($weatherDataArray);
    }
}

echo $weatherData;

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($weatherDataArray["sys"]["country"])) {
    $countryCode = $weatherDataArray["sys"]["country"];
    $countryFlag = "https://flagcdn.com/64x48/{$countryCode}.png";

    $sql = "SELECT * FROM pocasie WHERE country_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $countryCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sql = "UPDATE pocasie SET visit_counter = visit_counter + 1 WHERE country_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $countryCode);
        $stmt->execute();
        $sql = "INSERT INTO searched_locations (lat, lon) VALUES ($latitude, $longitude)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    } else {
        $sql = "INSERT INTO pocasie (country_name, flag_img, visit_counter) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $countryCode, $countryFlag);
        $stmt->execute();
        $sql = "INSERT INTO searched_locations (lat, lon) VALUES ($latitude, $longitude)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }    
    $stmt->close();
}
$conn->close();

?>
