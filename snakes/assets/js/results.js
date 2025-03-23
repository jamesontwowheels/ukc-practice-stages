window.onload = function() {

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${minutes}:${remainingSeconds.toString().padStart(2, "0")}`;
    }
    
    function renderTable(gameData) {
        const tableBody = document.querySelector("#gameTable tbody");
        tableBody.innerHTML = "";
    
        const validPlayers = Object.values(gameData).filter(player => player.params && player.params.score !== undefined);
    
        const sortedPlayers = validPlayers.sort((a, b) => {
            const totalA = a.params.score + Object.values(a.params.snake_score || {}).reduce((sum, s) => sum + s.score, 0);
            const totalB = b.params.score + Object.values(b.params.snake_score || {}).reduce((sum, s) => sum + s.score, 0);
            return totalB - totalA;
        });
    
        sortedPlayers.forEach(player => {
            const name = player.name;
            const bonusScore = player.score;
            const snakes = player.params.snakes || [];
            const snakeDetails = player.params.snake_score || {};
    
            const snakeScore = Object.values(snakeDetails).reduce((sum, snake) => sum + snake.score, 0);
            const totalScore = bonusScore + snakeScore;
            const snakeCount = snakes.length;
            let snakeInfo = "";
    
            if (snakeCount > 0) {
                snakeInfo = "<ul>";
                snakes.forEach(snakeID => {
                    const details = snakeDetails[snakeID];
                    if (details) {
                        const formattedTime = formatTime(details.time);
                        snakeInfo += `<li>S.${snakeID}: @ ${formattedTime}, L:${details.level} Score: ${details.score}</li>`;
                    }
                });
                snakeInfo += "</ul>";
            } else {
                snakeInfo = "No snakes captured";
            }
    
            const row = `
                <tr>
                    <td data-label="Team">${name}</td>
                    <td data-label="Snake Score">${snakeScore}</td>
                    <td data-label="Bonus">${bonusScore}</td>
                    <td data-label="Total">${totalScore}</td>
                    <td data-label="Snakes">${snakeCount}</td>
                    <td data-label="Details">${snakeInfo}</td>
                </tr>
            `;
    
            tableBody.innerHTML += row;
        });
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


 