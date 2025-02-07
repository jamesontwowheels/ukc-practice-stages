
const widgetID_2 = 2; // Replace with dynamic widget_ID if needed
console.log('ping 2');
// Function to fetch data and process it for the count per day
function fetchWidgetData2() {
    fetch(`assets/php/get_data.php?purpose=${widgetID_2}`) // Adjust API endpoint as needed
        .then(response => response.json())
        .then(data => {
            // Filter data where Purpose matches widgetID
            const filteredData = data.filter(item => item.Purpose == widgetID_2);
            console.log(filteredData);
            // Get the last 7 days in YYYY-MM-DD format
            const lastSevenDays = [...Array(7)].map((_, i) => {
                const date = new Date();
                date.setDate(date.getDate() - i);
                return date.toISOString().split("T")[0]; // Format: YYYY-MM-DD
            }).reverse(); // Reverse to show oldest first

            // Create an object to store the count of inputs for each day
            const dailyCounts = lastSevenDays.reduce((acc, date) => {
                acc[date] = 0; // Default to 0 count
                return acc;
            }, {});

            let totalLast7Days = 0; // Total count in the last 7 days
            let overallTotal = 0; // Overall total count (all-time)

            // Process each input entry
            filteredData.forEach(item => {
                const createdAt = new Date(item.CreatedAt);
                const entryDate = createdAt.toISOString().split("T")[0]; // Extract YYYY-MM-DD

                // If the entry date is in the last 7 days, increase the count for that day
                if (dailyCounts.hasOwnProperty(entryDate)) {
                    dailyCounts[entryDate] += 1;
                    totalLast7Days += 1;
                }

                // Count towards overall total
                overallTotal += 1;
            });

            // Create the chart and totals
            createBarChart2(lastSevenDays, Object.values(dailyCounts), totalLast7Days, overallTotal);
        })
        .catch(error => console.error("Error fetching data:", error));
}

// Function to create a bar chart showing daily input counts
function createBarChart2(labels, data, totalLast7Days, overallTotal) {
    // Create a container for the chart
    const widgetZone = document.getElementById("widget_zone_2");
    if (!widgetZone) {
        console.error("Element with ID 'widget_zone_2' not found.");
        return;
    }

    // Clear previous content
    widgetZone.innerHTML = "";

    // Create canvas for the chart
    const canvas = document.createElement("canvas");
    canvas.id = "widgetChart_2";
    widgetZone.appendChild(canvas);

    // Create the chart
    new Chart(canvas, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "Input Count",
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
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Display totals
    const totalContainer = document.createElement("div");
    totalContainer.className = "totals";
    totalContainer.innerHTML = `
        <p><strong>Total Inputs (Last 7 Days):</strong> ${totalLast7Days}</p>
        <p><strong>Overall Total Inputs:</strong> ${overallTotal}</p>
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
loadChartJS(fetchWidgetData2);
