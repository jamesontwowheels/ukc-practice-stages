(function () {
    const url = new URL(document.currentScript.src);
    const widgetID = url.searchParams.get("widget_ID");
    const start_date = url.searchParams.get("start_date");

    function fetchWidgetData() {
        fetch(`assets/php/get_data.php?widget_ID=${widgetID}`)
            .then(response => response.json())
            .then(data => {
                const filteredData = data.filter(item => item.widget_ID == widgetID);
                const startDate = new Date(start_date);
                renderCalendarView(filteredData, startDate, widgetID);
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    function renderCalendarView(filteredData, startDate, widgetID) {
        const widgetZone = document.getElementById("widget_zone_" + widgetID);
        if (!widgetZone) return;

        widgetZone.innerHTML = "";

        const calendarContainer = document.createElement("div");
        calendarContainer.className = "widget-calendar";

        const progressBarContainer = document.createElement("div");
        progressBarContainer.className = "progress-container";

        const progressBar = document.createElement("div");
        progressBar.className = "progress-bar";

        const progressFill = document.createElement("div");
        progressFill.className = "progress-fill";

        progressBar.appendChild(progressFill);
        progressBarContainer.appendChild(progressBar);
        calendarContainer.appendChild(progressBarContainer);

        const dailyStatus = {};
        filteredData.forEach(item => {
            const dateStr = new Date(item.CreatedAt).toISOString().split("T")[0];
            if (!dailyStatus[dateStr]) dailyStatus[dateStr] = false;
            if (item.InputValue == 1) dailyStatus[dateStr] = true;
        });

        const weeks = [];
        const calendarStart = new Date(startDate);
        calendarStart.setHours(0, 0, 0, 0);

        let totalAlcoholFreeDays = 0;

        for (let w = 0; w < 5; w++) {
            const weekRow = [];
            for (let d = 0; d < 7; d++) {
                const date = new Date(calendarStart);
                date.setDate(calendarStart.getDate() + w * 7 + d);
                weekRow.push(date);
            }
            weeks.push(weekRow);
        }

        weeks.forEach((week) => {
            const row = document.createElement("div");
            row.className = "calendar-week";

            let weeklyCount = 0;

            week.forEach(date => {
                const dateStr = date.toISOString().split("T")[0];
                const isAlcoholFree = dailyStatus[dateStr] === true;

                const dayBox = document.createElement("div");
                dayBox.className = "calendar-day " + (isAlcoholFree ? "alcohol-free" : "not-free");
                dayBox.textContent = date.getDate();
                if (isAlcoholFree) {
                    weeklyCount++;
                    totalAlcoholFreeDays++;
                }

                row.appendChild(dayBox);
            });

            const weekStatus = document.createElement("span");
            weekStatus.className = "week-check";
            weekStatus.textContent = weeklyCount >= 5 ? "✅" : "⚠️";
            weekStatus.title = `Alcohol-Free Days: ${weeklyCount}`;
            row.appendChild(weekStatus);

            calendarContainer.appendChild(row);
        });

        // Progress bar update
        const percent = Math.min((totalAlcoholFreeDays / 20) * 100, 100);
        progressFill.style.width = percent + "%";
        progressFill.textContent = `${Math.round(percent)}% Goal`;

        widgetZone.appendChild(calendarContainer);
    }

    fetchWidgetData();
})();
