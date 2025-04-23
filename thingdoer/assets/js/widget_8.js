
const widgetID_8 = 8; // Replace with dynamic widget_ID if needed
console.log('ping 8');
function fetchWidgetData8() {
    fetch(`assets/php/get_data.php?purpose=${widgetID_8}`)
        .then(response => response.json())
        .then(data => {
            const filteredData = data.filter(item => item.Purpose == widgetID_8);
            console.log(filteredData);

            const lastSevenDays = [...Array(7)].map((_, i) => {
                const date = new Date();
                date.setDate(date.getDate() - i);
                return date.toISOString().split("T")[0];
            }).reverse();

            // Initialize counts for each InputValue per day
            const dailyCounts = lastSevenDays.reduce((acc, date) => {
                acc[date] = { 1: 0, 2: 0, 3: 0 };
                return acc;
            }, {});

            let totalLast7Days = 0;
            let overallTotal = 0;

            filteredData.forEach(item => {
                const createdAt = new Date(item.CreatedAt);
                const entryDate = createdAt.toISOString().split("T")[0];
                const inputVal = parseInt(item.InputValue);

                if (dailyCounts.hasOwnProperty(entryDate) && [1, 2, 3].includes(inputVal)) {
                    dailyCounts[entryDate][inputVal] += 1;
                    totalLast7Days += 1;
                }

                overallTotal += 1;
                console.log(overallTotal);
            });

            // Separate data arrays for each input value
            const data1 = lastSevenDays.map(date => dailyCounts[date][1]);
            const data2 = lastSevenDays.map(date => dailyCounts[date][2]);
            const data3 = lastSevenDays.map(date => dailyCounts[date][3]);

            createBarChart8(lastSevenDays, data1, data2, data3, totalLast7Days, overallTotal);
        })
        .catch(error => console.error("Error fetching data:", error));
}

function createBarChart8(labels, data1, data2, data3, totalLast7Days, overallTotal) {
    const widgetZone = document.getElementById("widget_zone_8");
    console.log('running widget 8 functino');
    if (!widgetZone) {
        console.error("Element with ID 'widget_zone_8' not found.");
        return;
    }

    widgetZone.innerHTML = "";

    const canvas = document.createElement("canvas");
    canvas.id = "widgetChart_8";
    widgetZone.appendChild(canvas);

    new Chart(canvas, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Ice",
                    data: data3,
                    backgroundColor: "rgba(75, 192, 192, 0.6)"
                },
                {
                    label: "Legs",
                    data: data2,
                    backgroundColor: "rgba(255, 206, 86, 0.6)"
                },
                {
                    label: "Back",
                    data: data1,
                    backgroundColor: "rgba(255, 99, 132, 0.6)"
                }
            ]
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
loadChartJS(fetchWidgetData8);
