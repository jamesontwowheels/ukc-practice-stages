let countdownFunction = 0;
console.log("cf defined");

$(document).ready(ajax_call);
console.log("tested");
$("body").on("click", "button", ajax_call);

$(document).ready(function() {
    // On button click, toggle the expandable content
    $("#toggleButton").on("click", function() {
        var content = $("#expandableContent");
        console.log('expandable');

        if (content.hasClass("expand")) {
            // If expanded, collapse it
            content.removeClass("expand");
            content.css("height", "0");
        } else {
            // If collapsed, expand it
            content.addClass("expand");
            content.css("height", content.get(0).scrollHeight + "px"); // Expand to full height
        }
    });
});




function ajax_call() {
    if ($(this).hasClass('inactive')){

    } else {console.log("button clicked / name update");
    var cp = $(this).attr('cp');
    cp = cp || 0;
    console.log (cp);
    
    //bit of jazz
    var temp_highlight = $("#cp"+cp);
    temp_highlight.addClass('clicked');
    setTimeout(function() {
        temp_highlight.toggleClass('clicked');
    }, 2000);

    if (cp == 999){
        const userConfirmed = confirm("Are you sure you're ready to start/stop?")
        if(!userConfirmed) { return;};
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/geo/assets/php/test.php?cp='+cp,
        success: function(data) {
            console.log("ajax return");
            document.getElementById("inventory_zone").innerHTML = "Current word =" + data["current_word"] + "<br> Current bonus = " + data["current_bonus"];
            var cp_names = data["cp_names"];
            var cp_names_keys = Object.keys(cp_names);
            let i = 0;
            while (i < cp_names_keys.length) {
            var this_key = cp_names_keys[i];
            var cp_id = "butt" + this_key;
            document.getElementById(cp_id).innerHTML = cp_names[this_key];
            i++;
            } 
            
            //available CPs
            var available_cps = data["available_cps"];
            var all_cps = data["all_cps"];
            all_cps.forEach(element => {
                if (available_cps.includes(element)) {
                  console.log(`${element} is available.`);
                  rowId = "row"+element;
                 document.getElementById(rowId).style.display = 'block';
                } else {
                  console.log(`${element} is not available.`);
                  rowId = "row"+element;
                  document.getElementById(rowId).style.display = 'none';
                }
              });

            //game state
            var game_state = data["game_state"];
            console.log(game_state);
            var game_start = parseInt(game_state[1]);
            var game_end = parseInt(game_state[2]);
            var stage_time = game_state[3];
            if(game_state[0] === 1 && typeof countdownFunction > 0){
                    // Set the date and time we're counting down to
                const countdownDate = game_start + stage_time; // 5 minutes from now
                // Update the countdown every second
                    countdownFunction = setInterval(function() {

                console.log ("timer set! Interval = " + countdownFunction);
                    // Get the current date and time
                    const now = new Date().getTime();
                    const now_s = Math.floor(now/1000);
                    // Calculate the time difference between now and the countdown date
                    const distance = countdownDate - now_s;
                    console.log(now_s);
                    console.log(distance);

                    // Time calculations for minutes and seconds
                    const minutes = Math.floor((distance % (60 * 60)) / (60));
                    const seconds = Math.floor(distance % (60));

                    // Display the result in the element with id="timer"
                    document.getElementById("timer").innerHTML = minutes + "m " + seconds + "s ";
                },1000)} else if (game_state[0] == 2){

                console.log ("timer cancelled! Interval = " + countdownFunction);
                    clearInterval(countdownFunction);

                    
                    const finish_time = stage_time - (game_end - game_start);
                    console.log(finish_time);
                    console.log("game ended");
                    const minutes = Math.floor((finish_time % (60 * 60)) / (60));
                    const seconds = Math.floor(finish_time % (60));
                    document.getElementById("timer").innerHTML = minutes + "m " + seconds + "s ";
                    document.getElementById("timer").classList.add("complete");
                } else if (game_state[0] == 0){
                    document.getElementById("timer").classList.remove("complete");
                    document.getElementById("timer").innerHTML = "";
                }

            //running score
            document.getElementById("score_zone").innerHTML = data["running_score"];
            
            //commentary
            const items = data["commentary"];
            items.reverse();
            const itemList = document.getElementById('commentary-list');
            itemList.innerHTML = '';

                // Loop through the array and append each item to the DOM
                items.forEach(item => {
                    // Create a new DOM element (e.g., <li> for list items)
                    const listItem = document.createElement('li');
        
                    // Set the text content of the element to the array item
                    listItem.textContent = item;
        
                    // Append the new element to the target DOM element
                    itemList.appendChild(listItem);
            });
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
    });
}}

window.onload = function() {
    function getLeaderboard() {
        fetch('/geo/assets/php/test.php?cp=0') // Replace with your API endpoint
            .then(response => response.json())
            .then(data => {
                // Handle the successful response
                const live_scores = data["live_scores"];
                console.log(live_scores);
                updateLeaderboard(live_scores);
            })
            .catch(error => {
                // Handle the error response
                console.error('AJAX call error:', error);
            });
    }

    // Start the interval to make the AJAX call every 5 seconds
    const intervalId = setInterval(getLeaderboard, 5000);

    // Set a timeout to clear the interval after 2 hours (2 hours = 2 * 60 * 60 * 1000 milliseconds)
    const twoHoursInMilliseconds = 2 * 60 * 60 * 1000;
    setTimeout(function() {
        clearInterval(intervalId);
        console.log('Interval cleared after 2 hours');
    }, twoHoursInMilliseconds);
};

 // Reference to the tbody element
 const leadTableBody = document.querySelector('#leaderBoard_table tbody');

 function updateLeaderboard(leader_data) {
     leader_data.forEach(item => {
         // Check if a row with this key already exists
         const existingRow = document.querySelector(`#leaderBoard_table tbody tr[data-key="${item.key}"]`);

         if (existingRow) {
             // If the row exists, check if the value is different
             const existingValueCell = existingRow.querySelector('td:nth-child(2)');
             if (existingValueCell.textContent !== item.value) {
                 existingValueCell.textContent = item.value; // Update the value
             }
         } else {
             // If the row doesn't exist, create a new one
             const row = document.createElement('tr');
             row.setAttribute('data-key', item.key); // Set a data attribute for the key

             // Create the first column (key)
             const keyCell = document.createElement('td');
             keyCell.textContent = item.key;
             row.appendChild(keyCell);

             // Create the second column (value)
             const valueCell = document.createElement('td');
             valueCell.textContent = item.value;
             row.appendChild(valueCell);

             // Append the row to the table body
             leadTableBody.appendChild(row);
         }
     });
 }

