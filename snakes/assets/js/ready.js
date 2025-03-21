console.log (user_ID);
function checkGameReady() {
    if (user_ID === 29 || window.location_active && window.game_active && window.team_active) {
        console.log("The game is ready to go!");
        // Select the existing div by its ID
            const div = document.getElementById("go-to-start");

            // Create a button element
            const button = document.createElement("button");

            // Set button text
            button.textContent = "Go to Game";

            // Style the button (optional)
            button.style.padding = "10px 20px";
            button.style.fontSize = "16px";
            button.style.cursor = "pointer";

            // Add a click event to redirect to index.php
            button.addEventListener("click", function () {
                window.location.href = "index.php";
            });

            // Append the button to the selected div
            div.appendChild(button);
    } else {
        let missing = [];
        
        if (!window.location_active) missing.push("location_active");
        if (!window.game_active) missing.push("game_active");
        if (!window.team_active) missing.push("team_active");

        console.log("Waiting... Missing:", missing.join(", "));

        setTimeout(checkGameReady, 1000); // Retry after 1 second
    }
}

// Start the check
checkGameReady();
