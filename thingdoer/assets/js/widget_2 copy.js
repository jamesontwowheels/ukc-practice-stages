var params = new URLSearchParams(window.location.search);
console.log('URL Params:', params);

// Set widgetID for widget 2
var widgetID_2 = 2; 
console.log('Widget ID:', widgetID_2);

// Function to fetch data and process it for the chart
function fetchWidgetData() {
    fetch(`assets/php/get_data.php?purpose=${widgetID_2}`) // Adjust API endpoint as needed
        .then(response => response.json())
        .then(data => {
            console.log('Fetched Data:', data);
            
            // Filter data where Purpose matches widgetID
            const filteredData = data.filter(item => item.Purpose == widgetID_2);
            console.log(filteredData);
            if (filteredData.length === 0) {
                console.log('No data found for widgetID:', widgetID_2);
            }

            // Get the last 7 days in YYYY-MM-DD format
            const lastSevenDays = [...Array(7)].map((_, i) => {
                const date = new Date();
                date.setDate(date.getDate() - i);
                return date.toISOString().split("T")[0]; // Format: YYYY-MM-DD
            }).reverse(); // Reverse to show oldest first

            // Create an object to store minutes before 7:30 AM for each day
            const minutesBefore730 = lastSevenDays.reduce((acc, date) => {
                acc[date] = 0; // Default to 0
                return acc;
            }, {});

            let totalLast7Days = 0; // Total minutes before 7:30 AM in the last 7 days
            let overallTotal = 0; // Total minutes before 7:30 AM (all-time)

            // Process each input entry
            filteredData.forEach(item => {
                const createdAt = new Date(item.CreatedAt);
                const entryDate = createdAt.toISOString().split("T")[0]; // Extract YYYY-MM-DD

                const hours = createdAt.getHours();
                const minutes = createdAt.getMinutes();
                const timeInMinutes = hours * 60 + minutes;
                const cutoffTime = 7 * 60 + 30; // 7:30 AM in minutes

                if (timeInMinutes < cutoffTime) {
                    const minutesBefore = cutoffTime - timeInMinutes;

                    // If the entry date is in the last 7 days, add to daily totals
                    if (minutesBefore730.hasOwnProperty(entryDate)) {
                        minutesBefore730[entryDate] = minutesBefore;
                        totalLast7Days += minutesBefore;
                    }

                    // Count towards overall total
                    overallTotal += minutesBefore;
                }
            });

            // Create the chart
            createBarChart(lastSevenDays, Object.values(minutesBefore730), totalLast7Days, overallTotal);
        })
        .catch(error => {
            console.error("Error fetching data:", error);
        });
}

// Function to create a bar chart
function createBarChart(labels, data, totalLast7Days, overallTotal) {
    // Create a container for the chart (use widgetID to make it unique)
    const widgetZone = document.getElementById(`widget_zone_${widgetID_2}`);
    if (!widgetZone) {
        console.error(`Element with ID 'widget_zone_${widgetID_2}' not found.`);
        return;
    }

    // Clear previous content
    widgetZone.innerHTML = "";

    // Create canvas for the chart (use widgetID to make it unique)
    const canvas = document.createElement("canvas");
    canvas.id = `widgetChart_${widgetID_2}`;
    widgetZone.appendChild(canvas);
    console.log(canvas.id);

    // Create the chart
    new Chart(canvas, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "Count",
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
                        stepSize: 5
                    }
                }
            }
        }
    });

    // Display totals
    const totalContainer = document.createElement("div");
    totalContainer.className = "totals";
    totalContainer.innerHTML = `
        <p><strong>Total (Last 7 Days):</strong> ${totalLast7Days}</p>
        <p><strong>Overall Total:</strong> ${overallTotal}</p>
    `;
    widgetZone.appendChild(totalContainer);
}

// Load Chart.js library dynamically (if not already included)
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

// Load Chart.js first, then fetch data
loadChartJS(fetchWidgetData);
