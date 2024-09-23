let countdownFunction = 0;

$(document).ready(ajax_call);
$("body").on("click", "button", ajax_call);

function pause(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

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
    if ($(this).hasClass('inactive') || $(this).hasClass('blocked')){
        console.log('inactive clicked');
    } else {console.log("button clicked / name update");
    var cp = $(this).attr('cp');
    cp = cp || 0;
    console.log (cp);
    
    //bit of jazz
    $(this).addClass('blocked');
    var temp_highlight = $("#cp"+cp);
    temp_highlight.addClass('clicked');
    
    setTimeout(function() {
        temp_highlight.removeClass('clicked');
    }, 2000);

    if (cp == 999){
        const userConfirmed = confirm("Are you sure you're ready to start/stop?")
        if(!userConfirmed) { return;};
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/geo/assets/php/test.php?purpose=1&cp='+cp,
        success: function(data) {
            console.log("ajax return");
            var debug_log = data["debug_log"];
            console.log(debug_log);
            document.getElementById("inventory_zone").innerHTML = "Current word =" + data["current_word"] + "<br> Current bonus = " + data["current_bonus"];
            var cp_names = data["cp_names"];
            var cp_names_keys = Object.keys(cp_names);
            console.log(cp_names);
            let i = 0;
            while (i < cp_names_keys.length) {
            var this_key = cp_names_keys[i];
            var cp_id = "butt" + this_key;
            console.log(cp_id);

            //check that the element exists
            function checkElementExists(id, keyname, timeout = 15000) {
                return new Promise((resolve, reject) => {
                    const startTime = Date.now();
            
                    // Recursive function to check for the element
                    function check() {
                        const elapsedTime = Date.now() - startTime;
                        const element = document.getElementById(id);
            
                        if (element) {
                            console.log("Element found:", element);
                            element.innerHTML = keyname;
                            element.removeClass("blocked");
                            resolve(element); // Element found, resolve the promise
                        } else if (elapsedTime >= timeout) {
                            console.log("Timeout reached, stopping checks.");
                            reject(new Error("Element not found within timeout"));
                        } else {
                            console.log("Element not found, waiting 3 seconds...");
                            setTimeout(check, 3000); // Wait for 3 seconds and recheck
                        }
                    }
            
                    check(); // Start the checking process
                });
            }
            var keyname = cp_names[this_key];
            checkElementExists(cp_id, keyname, 15000).then( function() {
                console.log("trying to add " + cp_names[this_key])
                //document.getElementById(cp_id).innerHTML = cp_names[this_key];
            }).catch(error => {
                console.error(error.message);
            });
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
            if(game_state[0] == 1){
                    // Set the date and time we're counting down to
                const countdownDate = game_start + stage_time; // 5 minutes from now
                // Update the countdown every second
                    countdownFunction = setInterval(function() {
                    // Get the current date and time
                    const now = new Date().getTime();
                    const now_s = Math.floor(now/1000);
                    // Calculate the time difference between now and the countdown date
                    const distance = countdownDate - now_s;

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

            //upcoming letters
            var upcoming_letters = data['upcoming_letters'];
            var upcoming_letters_string = upcoming_letters[0] + "-" + upcoming_letters[1] + "-" + upcoming_letters[2] + "-" + upcoming_letters[3] + "-" + upcoming_letters[4];
            var lower_letters_string = upcoming_letters_string.toLowerCase();
            document.getElementById("upcoming_letters_zone").innerHTML = lower_letters_string;
            
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
        
    });
}}



