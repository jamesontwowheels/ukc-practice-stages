var params = new URLSearchParams(window.location.search);
console.log('URL Params:', params);

// Set widgetID for widget 2
var widgetID_1 = 15; 
console.log('Widget ID:', widgetID_1);

// Function to determine if a date is in BST
function isBST(date) {
    const year = date.getFullYear();

    // Last Sunday in March
    let lastSundayMarch = new Date(year, 2, 31);
    while (lastSundayMarch.getDay() !== 0) {
        lastSundayMarch.setDate(lastSundayMarch.getDate() - 1);
    }

    // Last Sunday in October
    let lastSundayOctober = new Date(year, 9, 31);
    while (lastSundayOctober.getDay() !== 0) {
        lastSundayOctober.setDate(lastSundayOctober.getDate() - 1);
    }

    return date >= lastSundayMarch && date < lastSundayOctober;
}

// Function to fetch data and process it for the chart
function fetchWidgetData() {
    fetch(`assets/php/get_data.php?widget_ID=${widgetID_1}`)
        .then(response => response.json())
        .then(data => {
            console.log('Fetched Data:', data);
            
            // Filter data where Purpose matches widgetID
            const filteredData = data.filter(item => item.widget_ID == widgetID_1);
            console.log(filteredData);
            if (filteredData.length === 0) {
                console.log('No data found for widgetID:', widgetID_1);
            }

            // HARD CODED CUTOFF: ignore all records before 8 December 2025
            const hardCutoff = new Date("2025-12-08T00:00:00");

            // Normalize cutoff to local midnight just to be safe
            hardCutoff.setHours(0,0,0,0);

            // === Build the visible date range: from max(today-6, hardCutoff) to today ===
            const today = new Date();
            today.setHours(0,0,0,0);

            // default 7-day start (today minus 6 days)
            let windowStart = new Date();
            windowStart.setDate(today.getDate() - 6);
            windowStart.setHours(0,0,0,0);

            // If the hard cutoff is later, start from the cutoff
            if (hardCutoff > windowStart) {
                windowStart = new Date(hardCutoff);
            }

            // Safeguard: don't let windowStart be after today
            if (windowStart > today) {
                windowStart = new Date(today);
            }

            // Build an array of dates from windowStart through today (inclusive)
            const lastDays = [];
            let cursor = new Date(windowStart);
            while (cursor <= today) {
                lastDays.push(cursor.toISOString().split("T")[0]);
                cursor.setDate(cursor.getDate() + 1);
            }

            console.log('Visible date range (start → end):', lastDays[0], '→', lastDays[lastDays.length-1]);

            // Default minutes-after-0600 = assume 08:00, i.e. 120 minutes
            const minutesAfter0600 = lastDays.reduce((acc, date) => {
                acc[date] = 120; // fallback if no press in that visible day
                return acc;
            }, {});

            let totalLastVisibleDays = 0;
            let overallTotalSinceCutoff = 0;

            // Temporary store earliest press per visible day (after cutoff)
            const earliestPress = {};

            // Process entries
            filteredData.forEach(item => {
                let createdAt = new Date(item.CreatedAt);

                // Adjust for BST
                if (isBST(createdAt)) {
                    createdAt.setHours(createdAt.getHours() + 1);
                }

                // Ignore anything before the hard cutoff date entirely
                // (this prevents any pre-cutoff records from influencing visible days)
                const createdAtMidnight = new Date(createdAt);
                createdAtMidnight.setHours(0,0,0,0);
                if (createdAtMidnight < hardCutoff) {
                    return;
                }

                const entryDate = createdAt.toISOString().split("T")[0];

                // Only keep earliest press per day (but only if that day is in our visible window)
                if (minutesAfter0600.hasOwnProperty(entryDate)) {
                    if (!earliestPress[entryDate] || createdAt < earliestPress[entryDate]) {
                        earliestPress[entryDate] = createdAt;
                    }
                }
            });

            // Now compute minutes after 06:00 (or fallback 120 for visible days with no press)
            Object.keys(minutesAfter0600).forEach(date => {
                if (earliestPress[date]) {
                    const press = earliestPress[date];
                    const timeInMinutes = press.getHours() * 60 + press.getMinutes();
                    const after = Math.max(0, timeInMinutes - 360); // 06:00 = 360 minutes
                    minutesAfter0600[date] = after;
                }

                totalLastVisibleDays += minutesAfter0600[date];
                overallTotalSinceCutoff += minutesAfter0600[date];
            });

            // Create the chart
            createBarChart1(lastDays, Object.values(minutesAfter0600), totalLastVisibleDays, overallTotalSinceCutoff);
        })
        .catch(error => {
            console.error("Error fetching data:", error);
        });
}

// Function to create a bar chart
function createBarChart1(labels, data, totalLastVisibleDays, overallTotalSinceCutoff) {
    const widgetZone = document.getElementById(`widget_zone_${widgetID_1}`);
    if (!widgetZone) {
        console.error(`Element with ID 'widget_zone_${widgetID_1}' not found.`);
        return;
    }

    widgetZone.innerHTML = "";

    const canvas = document.createElement("canvas");
    canvas.id = `widgetChart_${widgetID_1}`;
    widgetZone.appendChild(canvas);

    new Chart(canvas, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "Minutes After 06:00 (Earliest Press)",
                data: data,
                backgroundColor: "rgba(75, 192, 192, 0.6)",
                borderColor: "rgba(75, 192, 192, 1)",
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10
                    }
                }
            }
        }
    });

    function formatMinutes(total) {
        const minutes = total % 60;
        const totalHours = Math.floor(total / 60);
        const hours = totalHours % 24;
        const days = Math.floor(totalHours / 24);
    
        let formattedTime = [];
    
        if (days > 0) formattedTime.push(`${days} day${days !== 1 ? 's' : ''}`);
        if (hours > 0) formattedTime.push(`${hours} hour${hours !== 1 ? 's' : ''}`);
        if (minutes > 0 || total === 0)
            formattedTime.push(`${minutes} minute${minutes !== 1 ? 's' : ''}`);
    
        return formattedTime.join(", ");
    }

    let formattedMinutes = formatMinutes(overallTotalSinceCutoff);

    const totalContainer = document.createElement("div");
    totalContainer.className = "totals";
    totalContainer.innerHTML = `
        <p><strong>Total Minutes After 06:00 (Visible Days):</strong> ${totalLastVisibleDays}</p>
        <p><strong>Total Minutes After 06:00 (All-Time Since Cutoff):</strong> ${formattedMinutes}</p>
    `;
    widgetZone.appendChild(totalContainer);
}

// Load Chart.js if needed
function loadChartJS(callback) {
    if (typeof Chart !== "undefined") {
        callback();
        return;
    }

    const script = document.createElement("script");
    script.src = "https://cdn.jsdelivr.net/npm/chart.js";
    script.onload = callback;
    document.head.appendChild(script);
}

// Load Chart.js then fetch data
loadChartJS(fetchWidgetData);
