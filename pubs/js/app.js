const DATA_URL = "proxy.php";

let pubData = [];
let visited = JSON.parse(localStorage.getItem("visitedPubs") || "[]");

const map = L.map('map').setView([54.5, -2.5], 6); // UK-ish centre

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "&copy; OpenStreetMap contributors"
}).addTo(map);

const markers = {}; // store markers for updating color

async function loadData() {
  const response = await fetch(DATA_URL); //"data/pubs.xml");
  let xmlText = await response.text();

  // fix unescaped &
  xmlText = xmlText.replace(/&(?!(?:amp|lt|gt|quot|apos);)/g, "&amp;");

  const parser = new DOMParser();
  const doc = parser.parseFromString(xmlText, "text/xml");
  console.log(doc);

  const establishments = [...doc.querySelectorAll("EstablishmentDetail")];
  console.log(establishments);

  pubData = establishments.map(e => ({
    id: e.querySelector("FHRSID")?.textContent.trim(),
    name: e.querySelector("BusinessName")?.textContent.trim(),
    type: e.querySelector("BusinessTypeName, BusinessType")?.textContent.trim(),
    lat: parseFloat(e.querySelector("Geocode > Latitude")?.textContent),
    lng: parseFloat(e.querySelector("Geocode > Longitude")?.textContent)
  })).filter(p => p.type?.toLowerCase().includes("pub"));

  console.log(pubData);
  plotPubs();
  updateStats();
}

function plotPubs() {
  pubData.forEach(pub => {
    if (!pub.lat || !pub.lng) return;

    const marker = L.marker([pub.lat, pub.lng], {
      icon: L.icon({
        iconUrl: visited.includes(pub.id) ? 'https://maps.google.com/mapfiles/ms/icons/green-dot.png' : 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32]
      })
    }).addTo(map);

    markers[pub.id] = marker;

    const visitedText = visited.includes(pub.id) ? "(Visited)" : "";

    marker.bindPopup(`
      <strong>${pub.name}</strong> ${visitedText}<br/>
      <button onclick="toggleVisited('${pub.id}')">
        ${visited.includes(pub.id) ? 'Unmark Visited' : 'Mark Visited'}
      </button>
    `);
  });

  document.getElementById("totalCount").textContent = pubData.length;
}

function toggleVisited(id) {
  const index = visited.indexOf(id);
  if (index === -1) {
    visited.push(id);
    // change marker icon to green
    markers[id].setIcon(L.icon({
      iconUrl: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
      iconSize: [32, 32],
      iconAnchor: [16, 32]
    }));
  } else {
    visited.splice(index, 1);
    // change marker icon back to blue
    markers[id].setIcon(L.icon({
      iconUrl: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
      iconSize: [32, 32],
      iconAnchor: [16, 32]
    }));
  }

  localStorage.setItem("visitedPubs", JSON.stringify(visited));
  updateStats();
  // refresh popup content to reflect new state
  const marker = markers[id];
  marker.getPopup().setContent(`
    <strong>${pubData.find(p => p.id === id).name}</strong> ${visited.includes(id) ? "(Visited)" : ""}<br/>
    <button onclick="toggleVisited('${id}')">
      ${visited.includes(id) ? 'Unmark Visited' : 'Mark Visited'}
    </button>
  `);
}

function updateStats() {
  const visitedCount = visited.length;
  const total = pubData.length;

  document.getElementById("visitedCount").textContent = visitedCount;
  document.getElementById("totalCount").textContent = total;

  const percent = total ? Math.round((visitedCount / total) * 100) : 0;
  document.getElementById("completionPercent").textContent = percent + "%";
}

document.getElementById("locateMe").addEventListener("click", () => {
  navigator.geolocation.getCurrentPosition(pos => {
    const { latitude, longitude } = pos.coords;
    map.setView([latitude, longitude], 14);
  });
});

document.getElementById("nearestPubBtn").addEventListener("click", () => {
  navigator.geolocation.getCurrentPosition(pos => {
    const { latitude, longitude } = pos.coords;

    const unvisited = pubData.filter(p => !visited.includes(p.id));

    if (unvisited.length === 0) {
      document.getElementById("nearestPubResult").textContent = "You've visited all pubs!";
      return;
    }

    const closest = unvisited
      .map(pub => ({
        pub,
        dist: Math.sqrt(
          Math.pow(pub.lat - latitude, 2) +
          Math.pow(pub.lng - longitude, 2)
        )
      }))
      .sort((a, b) => a.dist - b.dist)[0].pub;

    document.getElementById("nearestPubResult").textContent =
      `Closest unvisited: ${closest.name}`;
  });
});

loadData();
