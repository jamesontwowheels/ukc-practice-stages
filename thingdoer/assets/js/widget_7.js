const widgetID_7 = 7; // Replace with dynamic widget_ID if needed
console.log('ping 7');

function fetchWidgetData7() {
    fetch(`assets/php/get_data.php?purpose=${widgetID_7}`)
        .then(response => response.json())
        .then(data => {
            const filteredData = data.filter(item => item.Purpose == widgetID_7);
            console.log(filteredData);

            const startDate = new Date("2025-04-03");
            const daysArray = Array.from({ length: 30 }, (_, i) => i + 1);
            
            const dailyEntries = filteredData.reduce((acc, item) => {
                const entryDate = new Date(item.CreatedAt).toISOString().split("T")[0];
                if (!acc[entryDate]) acc[entryDate] = { count: 0, hasValue1: false, hasValue2: false };
                acc[entryDate].count += 1;
                if (item.InputValue == 1) acc[entryDate].hasValue1 = true;
                if (item.InputValue == 2) acc[entryDate].hasValue2 = true;
                return acc;
            }, {});
            console.log (dailyEntries);
            renderGrid(daysArray, dailyEntries, startDate);
        })
        .catch(error => console.error("Error fetching data:", error));
}

function renderGrid(daysArray, dailyEntries, startDate) {
    const widgetZone = document.getElementById("widget_zone_7");
    if (!widgetZone) {
        console.error("Element with ID 'widget_zone_7' not found.");
        return;
    }
    
    widgetZone.innerHTML = "";
    const gridContainer = document.createElement("div");
    gridContainer.className = "grid-container";
    gridContainer.style.display = "grid";
    gridContainer.style.gridTemplateColumns = "repeat(6, 1fr)";
    gridContainer.style.gap = "10px";
    gridContainer.style.justifyContent = "center";
    gridContainer.style.padding = "10px";
    
    daysArray.forEach((dayNumber, index) => {
        const date = new Date(startDate);
        date.setDate(date.getDate() + index);
        const dateString = date.toISOString().split("T")[0];
        
        const dayElement = document.createElement("div");
        dayElement.className = "grid-item";
        dayElement.textContent = dayNumber;
        dayElement.style.width = "50px";
        dayElement.style.height = "50px";
        dayElement.style.display = "flex";
        dayElement.style.alignItems = "center";
        dayElement.style.justifyContent = "center";
        dayElement.style.borderRadius = "50%";
        dayElement.style.color = "white";
        dayElement.style.fontWeight = "bold";
        
        if (dailyEntries[dateString]) {
            if (dailyEntries[dateString].hasValue2) {
                dayElement.style.backgroundColor = "darkgreen";
                console.log('complete');
            } else if (dateString === startDate.toISOString().split("T")[0] && dailyEntries[dateString].hasValue1) {
                dayElement.style.backgroundColor = "lightgreen";
                dayElement.style.color = "black";
            } else {
                dayElement.style.backgroundColor = "green";
                console.log('override');
            }
        } else if (new Date(dateString) < new Date()) {
            dayElement.style.backgroundColor = "red";
        } else {
            dayElement.style.backgroundColor = "lightgray";
            dayElement.style.color = "black";
        }
        
        gridContainer.appendChild(dayElement);
    });
    
    widgetZone.appendChild(gridContainer);
}

fetchWidgetData7();
