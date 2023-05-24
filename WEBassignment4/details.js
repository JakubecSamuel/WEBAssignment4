function initMap() {
    const defaultCenter = { lat: 0, lng: 0 };
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 2,
        center: defaultCenter,
    });

    // Fetch locations data after the map has been initialized
    fetch("fetchLocations.php")
        .then(response => response.json())
        .then(data => {
            data.forEach(item => {
                const markerPosition = { lat: parseFloat(item.lat), lng: parseFloat(item.lon) };
                const marker = new google.maps.Marker({
                    position: markerPosition,
                    map: map,
                    title: "Location"
                });
            });
        })
        .catch(error => console.error("Error fetching locations:", error));
}


function getFlagUrl(countryCode) {
    return `https://flagcdn.com/64x48/${countryCode.toLowerCase()}.png`;
}

fetch("fetchDetails.php")
    .then(response => response.json())
    .then(data => {
        const tableBody = document.getElementById("details-table").querySelector("tbody");
        data.forEach(item => {
            const row = document.createElement("tr");

            const countryCell = document.createElement("td");
            countryCell.textContent = item.country_name;
            row.appendChild(countryCell);

            const flagCell = document.createElement("td");
            const flagUrl = getFlagUrl(item.country_name); // Use the country_name as the country code
            flagCell.innerHTML = `<img src="${flagUrl}" alt="${item.country_name} flag" width="64" height="auto">`;
            row.appendChild(flagCell);

            const visitsCell = document.createElement("td");
            visitsCell.textContent = item.visit_counter;
            row.appendChild(visitsCell);

            tableBody.appendChild(row);
        });
    })
    .catch(error => console.error("Error fetching details:", error));