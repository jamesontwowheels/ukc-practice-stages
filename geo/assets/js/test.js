$(document).ready(ajax_call);
console.log("tested");
$("body").on("click", "button", ajax_call);

function ajax_call() {
    console.log("button clicked / name update");
    var cp = $(this).attr('cp');
    cp = cp || 0;
    console.log (cp);
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
            
            //game state
            var game_state = data["game_state"];
            console.log(game_state);
            var game_start = game_state[1];
            var game_end = game_state[2];
            var stage_time = game_state[3];
            if(game_state[0] == 1){
                    // Set the date and time we're counting down to
                const countdownDate = game_start + stage_time; // 5 minutes from now

                // Update the countdown every second
                const countdownFunction = setInterval(function() {
                    // Get the current date and time
                    const now = new Date().getTime();
                    console.log(now);
                    // Calculate the time difference between now and the countdown date
                    const distance = countdownDate - now;

                    // Time calculations for minutes and seconds
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Display the result in the element with id="timer"
                    document.getElementById("timer").innerHTML = minutes + "m " + seconds + "s ";
                })}

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
}