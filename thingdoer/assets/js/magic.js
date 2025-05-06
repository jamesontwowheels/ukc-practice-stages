window.onload = function () {
    // Perform AJAX call to get widgets for existing user
    fetchWidgets();
};

function fetchWidgets() {
    // Make AJAX call to get_widgets.php - this returns all the widgets
    fetch('./assets/php/get_widgets.php')
        .then(response => response.json()) // Assuming JSON response
        .then(widgets => {
            // For each widget, create the DOM elements and insert into the widget container
            const widgetContainer = document.querySelector('.widget-container');
            widgets.forEach(widget => {
                const card = createWidgetCard(widget);
                widgetContainer.appendChild(card);

                // Optionally, run the relevant widget script (e.g., widget_1.js)
                runWidgetScript(widget);
                console.log("running widget script for widget id: "+widget.widget_ID);
            });
        })
        .catch(error => {
            console.error("Error fetching widgets:", error);
        });
}

function createWidgetCard(widget) {
    // Create widget card
    const card = document.createElement("div");
    card.classList.add("widget-card");

    // Header with widget name
    const header = document.createElement("header");
    const title = document.createElement("h3");
    title.textContent = widget.Name;
    header.appendChild(title);
    card.appendChild(header);

    // Widget description
    const description = document.createElement("p");
    description.textContent = widget.Description;
    card.appendChild(description);

    const widget_zone = document.createElement("div");
    widget_zone.setAttribute("id","widget_zone_"+widget.widget_ID)
    card.appendChild(widget_zone);

    // Footer with goal and button
    const footer = document.createElement("footer");
    const goal = document.createElement("span");
    goal.textContent = `Goal: ${widget.Goal}`;
    footer.appendChild(goal);

    const button = document.createElement("button");
    button.classList.add("widget-button");
    button.textContent = "Interact";
    footer.appendChild(button);

    card.appendChild(footer);

    return card;
}

function runWidgetScript(widget) {
    const script = document.createElement("script");
    console.log(widget.widget_ID);
    
    script.src = `assets/js/widget_${widget.Type}.js?widget_ID=${widget.widget_ID}&start_date=${widget.start_date}&t=${new Date().getTime()}`;  // Assuming the script is named widget_X.js
    console.log(`Loading script: ${script.src}`);
    document.body.appendChild(script);
}
