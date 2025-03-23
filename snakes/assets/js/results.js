window.onload = function() {


// Convert seconds to mm:ss format
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, "0")}`;
}

function renderTable(gameData) {
    const tableBody = document.querySelector("#gameTable tbody");
    tableBody.innerHTML = "";

    const validPlayers = Object.values(gameData).filter(player => player.score !== undefined);

    const sortedPlayers = validPlayers.sort((a, b) => {
        const totalA = (
            Object.values(a.params.snake_score || {}).reduce((sum, s) => sum + s.score, 0) +
            a.score +
            (a.params.location || 0)
        );
        const totalB = (
            Object.values(b.params.snake_score || {}).reduce((sum, s) => sum + s.score, 0) +
            b.score +
            (b.params.location || 0)
        );
        return totalB - totalA;
    });

    sortedPlayers.forEach(player => {
        const team = player.name;
        const bonus = player.score;  // Bonus = score
        const endPoint = player.params.location || 0;
        const snakes = player.params.snakes || [];
        const snakeDetails = player.params.snake_score || {};

        const snakeScore = Object.values(snakeDetails).reduce((sum, snake) => sum + snake.score, 0);
        const totalScore = snakeScore + bonus + endPoint;
        const snakeCount = snakes.length;
        let snakeInfo = "";

        if (snakeCount > 0) {
            snakeInfo = "<div class='snake-details'>";
            snakes.forEach(snakeID => {
                const details = snakeDetails[snakeID];
                if (details) {
                    const formattedTime = formatTime(details.time);
                    snakeInfo += `
                        <div class="snake-card">
                            <div class="snake-card-header">Snake ${snakeID}</div>
                            <div class="snake-card-body">
                                <p>Time: ${formattedTime}</p>
                                <p>Level: ${details.level}</p>
                                <p>Score: ${details.score}</p>
                            </div>
                        </div>
                    `;
                }
            });
            snakeInfo += "</div>";
        } else {
            snakeInfo = "No snakes captured";
        }

        const row = `
            <tr>
                <td data-label="Team">${team}</td>
                <td data-label="Snake Score">${snakeScore}</td>
                <td data-label="Bonus">${bonus}</td>
                <td data-label="End Point">${endPoint}</td>
                <td data-label="Total">${totalScore}</td> 
                <td data-label="Snakes">${snakeCount}</td>
                <td data-label="Details"><button class="" onclick="toggleDetails(this)">View Details</button>${snakeInfo}</td>
            </tr>
        `;

        tableBody.innerHTML += row;
    });
}

function toggleDetails(cell) {
    const details = cell.querySelector('.snake-details');
    if (details) {
        details.style.display = details.style.display === 'none' || details.style.display === '' ? 'block' : 'none';
    }
}


        console.log("made a fetch request");
        fetch('assets/php/test.php?purpose=2&cp=0') // Replace with your API endpoint
            .then(response => response.json())
            .then(data => {
                // Handle the successful response
                const teams = data["teams"];
                const debug = data["debug_log"];
                console.log(debug);
                console.log(teams);
                renderTable(teams);
            })
            .catch(error => {
                // Handle the error response
                console.error('AJAX call error:', error);
            });
};
// Expose toggleDetails to the global scope
window.toggleDetails = function (element) {
    console.log('clickeld');
    const details = element.nextElementSibling;
    console.log(details);
    if (details && details.classList.contains("snake-details")) {
        details.style.display = details.style.display === "none" ? "block" : "none";
    }
};

document.addEventListener("DOMContentLoaded", renderTable);
 