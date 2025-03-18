function checkGameReady() {
    if (window.location_active && window.game_active && window.team_active) {
        console.log("The game is ready to go!");
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
