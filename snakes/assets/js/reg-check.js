document.addEventListener("DOMContentLoaded", () => {
    const statusText = document.getElementById("reg-status");
    const joinBtn = document.getElementById("joinBtn");

    function checkRegistration() {
        fetch("assets/php/check_user.php")
            .then(response => response.json())
            .then(data => {
                if (data.registered) {
                    statusText.textContent = "You are already registered!";
                } else {
                    statusText.textContent = "You are not registered!";
                    joinBtn.style.display = "inline-block";
                }
            })
            .catch(error => {
                statusText.textContent = "Error checking registration.";
                console.error("Error:", error);
            });
    }

    joinBtn.addEventListener("click", () => {
        fetch("assets/php/join_game.php", { method: "POST" })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusText.textContent = "You have joined the game!";
                    joinBtn.style.display = "none";
                } else {
                    statusText.textContent = "Error joining game.";
                }
            })
            .catch(error => {
                statusText.textContent = "Error joining game.";
                console.error("Error:", error);
            });
    });

    checkRegistration();
});
