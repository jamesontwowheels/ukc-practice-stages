// ----------------------
// CONFIG / GOD MODE
// ----------------------
let d_base;
if (user_ID == 29) {
    d_base = 300;
    console.log("GOD MODE ACTIVE");
} else if (user_ID == 8) {
    d_base = 250000;
    console.log("demi-god mode active");
} else {
    d_base = 150;
}

// ----------------------
// INITIALIZE MAP
// ----------------------
const map = L.map('map', { zoomControl: true, tap: true }).setView([51.34, -0.27], 15);

L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

// ----------------------
// PLAYER MARKER
// ----------------------
const playerIcon = L.icon({
    iconUrl: 'assets/img/player.png', // replace with your player icon
    iconSize: [40, 40],
    iconAnchor: [20, 20],
});

const playerMarker = L.marker([0,0], { icon: playerIcon }).addTo(map);

const playerAccuracyCircle = L.circle([0,0], {
    radius: 0,
    color: '#3388ff',
    fillColor: '#3388ff',
    fillOpacity: 0.2,
}).addTo(map);

// ----------------------
// STORAGE FOR ELEMENT REFERENCES
// ----------------------
window.checkpointElements = {}; // { id: { button, label, markerDiv, marker } }

// ----------------------
// FETCH CHECKPOINTS
// ----------------------
fetch('assets/php/features.php')
    .then(r => r.json())
    .then(data => {
        const targets = data.features;
        window.targets = targets;
 const cp_options = document.querySelector('#cp_options');

        targets.forEach(target => {
            const id = target.properties.name;
            const [lng, lat] = target.geometry.coordinates;

            // ---------- 1) Create DivIcon marker HTML ----------
            const markerHtml = `
                <div id="marker-${id}" cp="${id}" class="checkpoint-marker">
                        <div class="pin">
                        </div>

                        <!-- Hidden info bubble (shown later on tap) -->
                        <div class="pin-info" id="cp-info-${id}">
                            <div class="pin-title" id="pin-title-${id}">${id}</div>
                            <div class="pin-distance" id="distance-${id}">${id}</div>
                        </div>
                    </div>
            `;

            // ---------- 2) Create DivIcon marker ----------
            const marker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: markerHtml,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                })
            }).addTo(map);

            // ---------- 3) Grab live DOM elements ----------
            const markerDiv = document.getElementById(`marker-${id}`);
            const button = markerDiv.querySelector(`#butt${id}`);
            const label = markerDiv.querySelector(`#cp${id}`);

            markerDiv.addEventListener('click', () => {
                markerDiv.classList.toggle('expanded');
            });

            // ---------- 4) Store references ----------
            window.checkpointElements[id] = {
                button,
                label,
                markerDiv,
                marker
            };

            console.log(checkpointElements);

            //------------- CP Options
            const optionCard = document.createElement('div');
            optionCard.id = `cp_option_card_${id}`;
            optionCard.innerHTML = `
                <div id="cp-header-${id}" class="cp-header">${id}</div>
                <span class="close-btn">&times;</span>
                <div id="cp_comment_space_${id}" class="cp_comment"></div>
                <div id="cp_info_space_${id}"></div>
                <div id="cp_option_space_${id}"></div>
            `;
            optionCard.classList.add('cp-option');
            cp_options.appendChild(optionCard);
        });

        // ---------- 5) Start geolocation watch ----------
        navigator.geolocation.watchPosition(success, error, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    })
    .catch(err => console.error("Failed to load checkpoints:", err));

// ----------------------
// DISTANCE + BEARING FUNCTIONS
// ----------------------
function deg2rad(d) { return d * Math.PI / 180; }

function calculateBearing(lat1, lon1, lat2, lon2) {
    const toRad = deg2rad;
    const toDeg = r => r * 180 / Math.PI;

    const lat1R = toRad(lat1);
    const lat2R = toRad(lat2);
    const dLon = toRad(lon2 - lon1);

    const y = Math.sin(dLon) * Math.cos(lat2R);
    const x = Math.cos(lat1R) * Math.sin(lat2R) - Math.sin(lat1R) * Math.cos(lat2R) * Math.cos(dLon);

    let brng = toDeg(Math.atan2(y, x));
    return (brng + 360) % 360;
}

function getCompassDirection(bearing) {
    const directions = ["North","North-East","East","South-East","South","South-West","West","North-West"];
    return directions[Math.round(bearing/45) % 8];
}

// ----------------------
// GEOLOCATION SUCCESS
// ----------------------
function success(pos) {
    const userLat = pos.coords.latitude;
    const userLon = pos.coords.longitude;
    const accuracy = Math.min(20, Math.round(pos.coords.accuracy));
    const d_need = d_base + accuracy;

    // ---------- 1) Update player marker ----------
    playerMarker.setLatLng([userLat, userLon]);
    playerAccuracyCircle.setLatLng([userLat, userLon]);
    playerAccuracyCircle.setRadius(accuracy);

    // Optional: follow player
    // map.setView([userLat, userLon], map.getZoom());

    const R = 6371; // Earth radius km

    // ---------- 2) Update all checkpoints ----------
    for (const target of window.targets) {
        const id = target.properties.name;
        const [lng, lat] = target.geometry.coordinates;

        const cp = window.checkpointElements[id];
        if (!cp) continue;

        // Haversine distance
        const dLat = deg2rad(userLat - lat);
        const dLon = deg2rad(userLon - lng);
        const a = Math.sin(dLat/2)**2 + Math.cos(deg2rad(userLat)) * Math.cos(deg2rad(lat)) * Math.sin(dLon/2)**2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const distance = Math.round(R * c * 1000); // meters

        const bearing = calculateBearing(userLat, userLon, lat, lng);
        const direction = getCompassDirection(bearing);

        // Update checkpoint label
        // cp.label.textContent = `${distance}m ${direction}`;

        var distance_div = document.getElementById("distance-"+id);
        console.log(distance_div + "distance-"+id);
        distance_div.innerText = `${distance}m ${direction}`;

        // Update button state
        if (distance < d_need) {
            cp.markerDiv.classList.add('active');
            cp.markerDiv.classList.remove('inactive');
        } else {
            cp.markerDiv.classList.remove('active');
            cp.markerDiv.classList.add('inactive');
            //cp.markerDiv.classList.remove('expanded');
            //cp.markerDiv.classList.add('small');
        }
    }
}

// ----------------------
// GEOLOCATION ERROR
// ----------------------
function error(err) {
    console.error(`ERROR(${err.code}): ${err.message}`);
}
