window.onload = function() {

 // Reference to the tbody element
 const leadTableBody = document.querySelector('#leaderBoard_table tbody');

    function updateLeaderboard(leader_data) {
        leader_data.forEach(item => {
            // Check if a row with this key already exists
            const existingRow = document.querySelector(`#leaderBoard_table tbody tr[data-key="${item[0]}"]`);
   
            if (existingRow) {
                // If the row exists, check if the value is different
                const existingValueCell = existingRow.querySelector('td:nth-child(2)');
                if (existingValueCell.textContent !== item[1]) {
                    existingValueCell.textContent = item[1]; // Update the value
                }
            } else {
                // If the row doesn't exist, create a new one
                const row = document.createElement('tr');
                row.setAttribute('data-key', item[0]); // Set a data attribute for the key
   
                // Create the first column (key)
                const keyCell = document.createElement('td');
                keyCell.textContent = item[0];
                row.appendChild(keyCell);
   
                // Create the second column (value)
                const valueCell = document.createElement('td');
                valueCell.textContent = item[1];
                row.appendChild(valueCell);
   
                // Append the row to the table body
                leadTableBody.appendChild(row);
            }
        });
    }

        console.log("made a fetch request");
        fetch('assets/php/test.php?purpose=2&cp=0') // Replace with your API endpoint
            .then(response => response.json())
            .then(data => {
                // Handle the successful response
                const live_scores = data["live_scores"];
                console.log(live_scores);
                const usernames = data["usernames"];
                const arrayOfPairs = Object.entries(live_scores);
                console.log(arrayofPairs);
                updateLeaderboard(arrayOfPairs);
            })
            .catch(error => {
                // Handle the error response
                console.error('AJAX call error:', error);
            });
};


 