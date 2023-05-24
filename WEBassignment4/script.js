document.getElementById("search").addEventListener("click", () => {
    const cityName = document.getElementById("city").value;
    if (cityName) {
        fetch("getWeather.php?city=" + cityName)
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error("Failed to fetch weather data");
                }
            })
            .then(data => displayWeather(data))
            .catch(error => {
                console.error(error);
                alert("Error fetching weather data. Please try again.");
            });
    }
});

function displayWeather(data) {
    document.getElementById("city-name").textContent = data.name;
    switch (data.weather[0].main) {
        case 'Clear':
            document.getElementById("weather-icon").src = `images/clear.png`;
            break;

        case 'Rain':
            document.getElementById("weather-icon").src = `images/rain.png`;
            break;

        case 'Snow':
            document.getElementById("weather-icon").src = `images/snow.png`;
            break;

        case 'Clouds':
            document.getElementById("weather-icon").src = `images/cloud.png`;
            break;

        case 'Haze':
            document.getElementById("weather-icon").src = `images/mist.png`;
            break;

        default:
            document.getElementById("weather-icon").src = ``;
    }
    document.getElementById("weather-description").textContent = data.weather[0].description;
    document.getElementById("temperature").textContent = `${data.main.temp} °C`;
    fetch(`https://restcountries.com/v3.1/alpha/${data.sys.country}`)
    .then(response => response.json())
    .then(countryData => {
        document.getElementById("country-flag").innerHTML = `<img src="${countryData[0].flags.png}" alt="${data.sys.country} flag" width="64" height="auto">`;
    })
    .catch(error => console.error('Error fetching country flag:', error));
    document.getElementById("coordinates").textContent = `GPS: ${data.coord.lat}, ${data.coord.lon}`;

    document.getElementById("weather-info").style.display = "block";
}

function getCurrentWeather() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(fetchWeatherData, showError);
        fetch("updateCas.php").catch(error => console.error("Error updating cas table:", error));
    } else {
        document.getElementById("weatherResult").innerHTML = "Geolocation is not supported by this browser.";
    }
}

function fetchWeatherData(position) {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;
    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const weatherData = JSON.parse(xhr.responseText);
            displayWeatherData(weatherData);
        }
    };

    xhr.open("GET", `get_weather.php?lat=${lat}&lon=${lon}`, true);
    xhr.send();
}

function displayWeatherData(data) {
    document.getElementById("city-name").textContent = data.name;
    switch (data.weather[0].main) {
        case 'Clear':
            document.getElementById("weather-icon").src = `images/clear.png`;
            break;

        case 'Rain':
            document.getElementById("weather-icon").src = `images/rain.png`;
            break;

        case 'Snow':
            document.getElementById("weather-icon").src = `images/snow.png`;
            break;

        case 'Clouds':
            document.getElementById("weather-icon").src = `images/cloud.png`;
            break;

        case 'Haze':
            document.getElementById("weather-icon").src = `images/mist.png`;
            break;

        default:
            document.getElementById("weather-icon").src = ``;
    }
    document.getElementById("weather-description").textContent = data.weather[0].description;
    document.getElementById("temperature").textContent = `${data.main.temp} °C`;
    fetch(`https://restcountries.com/v3.1/alpha/${data.sys.country}`)
    .then(response => response.json())
    .then(countryData => {
        document.getElementById("country-flag").innerHTML = `<img src="${countryData[0].flags.png}" alt="${data.sys.country} flag" width="64" height="auto">`;
    })
    .catch(error => console.error('Error fetching country flag:', error));
    document.getElementById("coordinates").textContent = `GPS: ${data.coord.lat}, ${data.coord.lon}`;

    document.getElementById("weather-info").style.display = "block";
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            document.getElementById("weatherResult").innerHTML = "User denied the request for Geolocation.";
            break;
        case error.POSITION_UNAVAILABLE:
            document.getElementById("weatherResult").innerHTML = "Location information is unavailable.";
            break;
        case error.TIMEOUT:
            document.getElementById("weatherResult").innerHTML = "The request to get user location timed out.";
            break;
        case error.UNKNOWN_ERROR:
            document.getElementById("weatherResult").innerHTML = "An unknown error occurred.";
            break;
    }
}
