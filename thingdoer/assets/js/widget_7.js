(function() {
    const url = new URL(document.currentScript.src);
    const widgetID = url.searchParams.get("widget_ID");
    const start_date = url.searchParams.get("start_date");

    let isFetching = false;

    function fetchWidgetData() {
        if (isFetching) return;
        isFetching = true;

        fetch(`assets/php/get_data.php?widget_ID=7`)
            .then(response => response.json())
            .then(data => {
                const filteredData = data.filter(item => item.widget_ID == widgetID);
                const startDate = new Date(start_date);
                const daysArray = Array.from({ length: 30 }, (_, i) => i + 1);
                
                const dailyEntries = filteredData.reduce((acc, item) => {
                    const entryDate = new Date(item.CreatedAt).toISOString().split("T")[0];
                    if (!acc[entryDate]) acc[entryDate] = { count: 0, hasValue1: false, hasValue2: false };
                    acc[entryDate].count += 1;
                    if (item.InputValue == 1) acc[entryDate].hasValue1 = true;
                    if (item.InputValue == 2) acc[entryDate].hasValue2 = true;
                    return acc;
                }, {});
                
                renderGrid(daysArray, dailyEntries, startDate, widgetID);
            })
            .catch(error => console.error("Error fetching data:", error))
            .finally(() => {
                isFetching = false;
            });
    }

    function renderGrid(daysArray, dailyEntries, startDate, widgetID) {
        const targetId = "widget_zone_" + widgetID;
        const widgetZone = document.getElementById(targetId);
        if (!widgetZone) {
            console.error("Element with ID " + targetId + " not found.");
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
            const todayDate = new Date().toISOString().split("T")[0];

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
                } else if (dateString === todayDate && dailyEntries[dateString].hasValue1) {
                    dayElement.style.backgroundColor = "lightgreen";
                    dayElement.style.color = "black";
                } else {
                    dayElement.style.backgroundColor = "green";
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

    // Start fetching when script runs
    fetchWidgetData();
})();
