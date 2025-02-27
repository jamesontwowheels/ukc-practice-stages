let countdown = 10;
let interval;
const totalDashArray = 283; // Circumference of the circle
const countdownCircle = document.getElementById("countdown-progress");
const countdownText = document.getElementById("countdown-text");
const statusMessage = document.getElementById("status-message");

function checkGameStatus() {
    fetch('assets/php/check_game_status.php')
        .then(response => response.json())
        .then(data => {
            if (data.started) {
                statusMessage.innerHTML = "The game has started! 🎉";
                countdownCircle.style.stroke = "#e74c3c"; // Change to red when game starts
                countdownText.textContent = "GO!";
                clearInterval(interval); // Stop polling
            } else {
                resetCountdown();
            }
        })
        .catch(error => console.error("Error checking game status:", error));
}

function resetCountdown() {
    countdown = 10;
    countdownText.textContent = countdown;
    updateCircle();
}

function updateCircle() {
    let offset = (countdown / 10) * totalDashArray;
    countdownCircle.style.strokeDashoffset = totalDashArray - offset;
}

function startCountdown() {
    interval = setInterval(() => {
        countdown--;
        countdownText.textContent = countdown;
        updateCircle();

        if (countdown === 0) {
            checkGameStatus();
        }
    }, 1000);
}

// Initial UI Setup
resetCountdown();
checkGameStatus();
startCountdown();
