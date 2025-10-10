
if (user_ID == 29 || user_ID == 50) {
    console.log("The game is ready to go!");
    // Select the existing div by its ID
        const div2 = document.getElementById("unlock-button");

        // Create a button element
        const button = document.createElement("button");

        // Set button text
        button.textContent = "Unlock Game";


        // Add a click event to redirect to index.php
        button.addEventListener("click", function () {
            window.location.href = "assets/php/start_game.php";
        });

        // Append the button to the selected div
        div2.appendChild(button);
    }