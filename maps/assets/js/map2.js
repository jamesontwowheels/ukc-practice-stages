// ------------------------------------------------------
// USER RANGE SETTINGS (your GOD MODE logic)
// ------------------------------------------------------
let d_base;
if (user_ID == 29) {
    d_base = 3000000000000;
    console.log("GOD MODE ACTIVE");
} else if (user_ID == 8) {
    d_base = 250000;
    console.log("demi-god mode active");
} else {
    d_base = 15000;
}


// ------------------------------------------------------
// LEAFLET MAP INITIALIZATION
// ------------------------------------------------------
const map = L.map('map', {
    zoomControl: true,
    tap: true
}).setView([51.34, -0.27], 15);

L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(map);


// ------------------------------------------------------
// HELPERS
// ------------------------------------------------------
function deg2rad(d) {
    return d * (Math.PI / 180);
}

function calculateBearing(lat1, lon1, lat2, lon2) {
    const toRad = deg2rad;
    const toDeg = (r) => r * (180 / Math.PI);

    const lat1R = toRad(lat1);
    const lat2R = toRad(lat2);
    const dLon = toRad(lon2 - lon1);

    const y = Math.sin(dLon) * Math.cos(lat2R);
    const x = Math.cos(lat1R) * Math.sin(lat2R) -
              Math.sin(lat1R) * Math.cos(lat2R) * Math.cos(dLon);

    let brng = toDeg(Math.atan2(y, x));
    return (brng + 360) % 360;
}

function getCompassDirection(bearing) {
    const dirs = [
        "North", "North-East", "East", "South-East",
        "South", "South-West", "West", "North-West"
    ];
    return dirs[Math.round(bearing / 45) % 8];
}


// ------------------------------------------------------
//   GPS BUTTON
// ------------------------------------------------------
document.getElementById("gps_button").addEventListener("click", () => {
    map.locate({ setView: true, maxZoom: 18 });
});

map.on("locationfound", (e) => {
    L.circleMarker(e.latlng, {
        radius: 8,
        color: "#007bff"
    }).addTo(map);
});


// ------------------------------------------------------
//   LOAD CHECKPOINTS
// ------------------------------------------------------
let targets = [];

fetch("assets/php/features.php")
    .then(r => r.json())
    .then(data => {
        targets = data.features;

        const cp_options = document.querySelector("#cp_options");

        targets.forEach(feature => {

            const id = feature.properties.name;
            const [lng, lat] = feature.geometry.coordinates;

            // Create CP card
            const card = document.createElement("div");
            card.id = `cp_option_card_${id}`;
            card.className = "cp-option";
            card.innerHTML = `
                <div class="cp-header">${id}</div>
                <span class="close-btn" onclick="closeOptionCard('${id}')">&times;</span>
                <div id="cp_comment_space_${id}" class="cp_comment"></div>
                <div id="cp_info_space_${id}"></div>
                <div id="cp_option_space_${id}"></div>
            `;
            cp_options.appendChild(card);

            // Create CP button
            const cpButton = document.createElement("button");
            cpButton.id = `butt${id}`;
            cpButton.className = "cp_button inactive";
            cpButton.textContent = id;

            cpButton.addEventListener("click", () => {
                showOptionCard(id);
            });

            // Add marker
            const marker = L.marker([lat, lng]).addTo(map);

            // Popup button clone
            const popupBtn = cpButton.cloneNode(true);
            popupBtn.addEventListener("click", () => cpButton.click());

            const popupDiv = document.createElement("div");
            popupDiv.appendChild(popupBtn);
            marker.bindPopup(popupDiv);

            // Store for later distance checking
            window[`cp_${id}`] = {
                id,
                lat,
                lng,
                button: cpButton,
                card,
                marker
            };
        });

        // Start watching GPS
        navigator.geolocation.watchPosition(gpsSuccess, gpsError, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    });



// -------------------------------------------------------------------
// GPS SUCCESS — Your distance + bearing + activation logic integrated
// -------------------------------------------------------------------
function gpsSuccess(pos) {

    const userLat = pos.coords.latitude;
    const userLon = pos.coords.longitude;

    const accuracy = Math.min(20, Math.round(pos.coords.accuracy));
    const d_need = d_base + accuracy;

    const R = 6371; // km

    targets.forEach(feature => {

        const id = feature.properties.name;
        const [lng, lat] = feature.geometry.coordinates;

        // Get elements
        const cpButton = document.getElementById(`butt${id}`);
        const card = document.getElementById(`cp_option_card_${id}`);

        // Distance
        const dLat = deg2rad(userLat - lat);
        const dLon = deg2rad(userLon - lng);

        const a =
            Math.sin(dLat/2) ** 2 +
            Math.cos(deg2rad(userLat)) *
            Math.cos(deg2rad(lat)) *
            Math.sin(dLon/2) ** 2;

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const dist = Math.round(R * c * 1000); // meters

        // Bearing & direction
        const bearing = calculateBearing(userLat, userLon, lat, lng);
        const direction = getCompassDirection(bearing);

        // Update CP button text
        cpButton.textContent = `${dist}m ${direction}`;

        // Check range
        if (dist < d_need) {
            cpButton.classList.add("active");
            cpButton.classList.remove("inactive");
        } else {
            cpButton.classList.remove("active");
            cpButton.classList.add("inactive");

            // Hide the card if open
            card.classList.remove("active");
        }

    });
}


// -------------------------------------------------------------------
// GPS ERROR
// -------------------------------------------------------------------
function gpsError(err) {
    alert("location check failed. Code: " + err.code + " Message:" + err.message);
    console.error(`ERROR(${err.code}): ${err.message}`);
}


// -------------------------------------------------------------------
// CARD UI
// -------------------------------------------------------------------
function showOptionCard(id) {
    const card = document.getElementById(`cp_option_card_${id}`);
    card.classList.add("active");
}

function closeOptionCard(id) {
    const card = document.getElementById(`cp_option_card_${id}`);
    card.classList.remove("active");
}
