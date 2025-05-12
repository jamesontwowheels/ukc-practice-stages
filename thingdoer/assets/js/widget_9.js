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
                const dailyMap = {};

                filteredData.forEach(item => {
                    const createdAt = new Date(item.CreatedAt);
                    const dateKey = createdAt.toISOString().split("T")[0];
                    const hour = createdAt.getHours();

                    if (!dailyMap[dateKey]) {
                        dailyMap[dateKey] = { pledged: false, completed: false };
                    }

                    if (hour < 12) {
                        dailyMap[dateKey].pledged = true;
                    } else {
                        dailyMap[dateKey].completed = true;
                    }
                });

                renderCalendar(startDate, dailyMap, widgetID);
            })
            .catch(console.error);
    }

    function renderCalendar(startDate, dailyMap, widgetID) {
        const zone = document.getElementById("widget_zone_" + widgetID);
        if (!zone) return;

        zone.innerHTML = "";
        const container = document.createElement("div");
        container.className = "calendar-container";

        const now = new Date();
        const todayDate = now.toISOString().split("T")[0];
        const totalWeeks = 5;
        const targetPerWeek = 5;
        let weeklyData = [];

        for (let w = 0; w < totalWeeks; w++) {
            const week = [];
            let total = 0;

            for (let d = 0; d < 7; d++) {
                const date = new Date(startDate);
                date.setDate(date.getDate() + w * 7 + d);
                const dateStr = date.toISOString().split("T")[0];
                const status = dailyMap[dateStr] || { pledged: false, completed: false };

                const day = document.createElement("div");
                day.className = "calendar-day";
                day.textContent = date.getDate();

                const isPast = dateStr < todayDate;
                const isToday = dateStr === todayDate;

                if (isPast) {
                    if (status.completed) {
                        day.classList.add("alcohol-free");
                        total += 1;
                    } else {
                        day.classList.add("missed-day");
                    }
                } else if (isToday) {
                    if (status.completed) {
                        day.classList.add("alcohol-free");
                        total += 1;
                    } else if (status.pledged) {
                        day.classList.add("pledged");
                    } // else stay neutral
                } else {
                    day.classList.add("future-day");
                }

                week.push(day);
            }

            const weekRow = document.createElement("div");
            weekRow.className = "calendar-week";

            week.forEach(el => weekRow.appendChild(el));

            const startOfWeek = new Date(startDate);
            startOfWeek.setDate(startOfWeek.getDate() + w * 7);
            const startKey = startOfWeek.toISOString().split("T")[0];

            if (startKey < todayDate) {
                const status = document.createElement("div");
                status.className = "week-status";
                status.textContent = `✔️ ${total}/${targetPerWeek}`;
                status.classList.add(total >= targetPerWeek ? "success" : "fail");
                weekRow.appendChild(status);
            }

            container.appendChild(weekRow);
            weeklyData.push(total);
        }

        const totalTarget = targetPerWeek * totalWeeks;
        const totalAchieved = weeklyData.reduce((a, b) => a + b, 0);
        const percent = Math.min(100, Math.round((totalAchieved / totalTarget) * 100));

        const progressWrap = document.createElement("div");
        progressWrap.className = "progress-wrapper";

        const progressBar = document.createElement("div");
        progressBar.className = "progress-bar";
        progressBar.style.width = percent + "%";
        progressBar.textContent = percent + "%";

        progressWrap.appendChild(progressBar);
        zone.appendChild(progressWrap);
        zone.appendChild(container);
    }

    fetchWidgetData();
})();
