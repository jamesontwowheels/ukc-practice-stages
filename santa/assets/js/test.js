let countdownFunction = 0;
let oxygenFunction = 0;
var puzzle_questions = [];

$(document).ready(ajax_call);
$("body").on("click", "button", ajax_call);

function pause(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showTemporaryMessage(message, duration) {
    const tempMessage = document.getElementById('tempMessage');
    
    // Set the message
    tempMessage.textContent = message;
    
    // Display the message
    tempMessage.style.display = 'block';
    tempMessage.style.opacity = '1';
    
    // Hide the message after the specified duration
    setTimeout(() => {
      tempMessage.style.opacity = '0'; // Fade out
      setTimeout(() => {
        tempMessage.style.display = 'none'; // Hide after fade out
      }, 500); // Matches the fade-out transition duration
    }, duration);
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
    
    var user_input = "void";
    if($(this).hasClass('puzzle')){
        user_input = prompt(puzzle_questions[cp]);
        if (user_input === null || user_input == "") { 
            console.log("nothing input");
            return;}
    } else{ console.log("no puzzle"); }

    var cp = $(this).attr('cp');
    cp = Number.isInteger(Number(cp)) ? Number(cp) : 0;
    console.log (cp);
   
    if (cp == 999){
        const userConfirmed = confirm("Are you sure you're ready to start/stop?")
        if(!userConfirmed) { return;};
    }

    //bit of jazz
    $(this).addClass('blocked');
    var temp_highlight = $("#cp"+cp);
    temp_highlight.addClass('clicked');
    
    setTimeout(function() {
        temp_highlight.removeClass('clicked');
    }, 2000);

   

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'assets/php/test.php?purpose=1&cp='+cp+'&user_input='+user_input,
        success: function(data) {
            console.log("ajax return");
            var debug_log = data["debug_log"];
            console.log(debug_log);
            var inventory = data["inventory"];
            var cp_names = data["cp_names"];
            var cp_names_keys = Object.keys(cp_names);
            var available_cps = data["available_cps"];
            var puzzle_cps = data["puzzle_cps"];
            var comment = data["comment"];
            let i = 0;
            showTemporaryMessage(comment, 3000);
            //inventories:
                // Count occurrences of each key
                var keys1 = inventory[0];
                var keys2 = inventory[1];
                console.log(inventory);
                var names = cp_names;
                // Function to count occurrences and generate HTML
                function generateList(keys, names) {
                    var counts = keys.reduce((acc, key) => {
                        acc[key] = (acc[key] || 0) + 1;
                        return acc;
                    }, {});
                    return Object.entries(counts)
                        .map(([key, count]) => `<li>${names[key]}: ${count}</li>`)
                        .join("");
                }

                // Output to different lists
                if(keys1 !== null){document.getElementById("output_resources").innerHTML = generateList(keys1, names);}
                if(keys2 !== null){document.getElementById("output_gifts").innerHTML = generateList(keys2, names);}

            //puzzle response
            var puzzle_response = data["puzzle_response"];
            if(puzzle_response == 1){
                alert("Yes! Puzzle correct");
            } else if(puzzle_response == 2){
                alert ("No. Puzzle incorrect");
            }

            while (i < cp_names_keys.length) {
            var this_key = cp_names_keys[i];
            var cp_id = "butt" + this_key;

           

            //check that the element exists
            function checkElementExists(id, key_id, keyname, timeout = 15000) {
                return new Promise((resolve, reject) => {
                    const startTime = Date.now();
            
                    // Recursive function to check for the element
                    function check() {
                        const elapsedTime = Date.now() - startTime;
                        const element = document.getElementById(id);
            
                        if (element) {
                            console.log("Element found:", element);
                            element.innerHTML = keyname;
                            element.classList.remove("blocked");
                            element.classList.remove("puzzle");
                            if (available_cps.includes(parseInt(key_id))) {
                                rowId = "row"+key_id;
                               document.getElementById(rowId).style.display = 'block';
                              } else {
                                rowId = "row"+key_id;
                                document.getElementById(rowId).style.display = 'none';
                              }
                            if(puzzle_cps.includes(parseInt(key_id))){
                                var puzzle_butt = "butt"+key_id;
                                document.getElementById(puzzle_butt).classList.add("puzzle");
                            }
                            resolve(element); // Element found, resolve the promise
                        } else if (elapsedTime >= timeout) {
                            console.log("Timeout reached, stopping checks.");
                            reject(new Error("Element not found within timeout"));
                        } else {
                            console.log("Element not found, waiting 3 seconds...");
                            setTimeout(check, 250); // Wait for 3 seconds and recheck //THIS ISNT THE TIME TO FIX IT,,, but this is BAD! IT SETS ADDITONALY CHECK like CRZY!!
                        }
                    }
            
                    check(); // Start the checking process
                });
            }
            var keyname = cp_names[this_key];
            checkElementExists(cp_id, this_key, keyname, 15000).then( function() {
                console.log("trying to add " + cp_names[this_key])
                //document.getElementById(cp_id).innerHTML = cp_names[this_key];
            }).catch(error => {
                console.error(error.message);
            });
            i++;
            } 
            
           //puzzle CPs
            //remove the puzzles first
            const puzzle_elements = document.querySelectorAll(".puzzle");
            puzzle_elements.forEach(function(element) {
                element.classList.remove("puzzle");
            });

            // Loop through the array
            puzzle_cps.forEach(function(element) {
                // Construct the ID by appending 'row' to the current element
                const elementId = "butt" + element;
                
                // Select the element by its ID
                const targetElement = document.getElementById(elementId);
                
                // Check if the element exists in the DOM
                if (targetElement) {
                    // Add the "puzzle" class to the selected element
                    targetElement.classList.add("puzzle");
                } else {
                    console.log(`Element with ID "${elementId}" not found.`);
                }
            });
            
            //alert feedback
            var alert_response = data["alert"];
            console.log(alert_response);
            if (alert_response != 0) {
                alert(alert_response);
            }

            //puzzle questions
            puzzle_questions = data["puzzle_questions"];
            
            //game state
            var game_state = data["game_state"];
            console.log(game_state);
            var game_start = parseInt(game_state[1]);
            var game_end = parseInt(game_state[2]);
            var stage_time = game_state[3];
            if(game_state[0] == 1){
                document.getElementById("teams").style.display = "none";
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
                    var minutes = Math.floor((distance) / (60));
                    var seconds = Math.floor(distance % (60));

                    // Display the result in the element with id="timer"
                    document.getElementById("timer").innerHTML = minutes + "m " + seconds + "s ";
                },1000)} else if (game_state[0] == 2){

                console.log ("timer cancelled! Interval = " + countdownFunction);
                    clearInterval(countdownFunction);

                    
                    const finish_time = stage_time - (game_end - game_start);
                    console.log(finish_time);
                    console.log("game ended");
                    var end_minutes = Math.floor((finish_time) / (60));
                    var end_seconds = Math.floor(finish_time % (60));
                    document.getElementById("timer").innerHTML = end_minutes + "m " + end_seconds + "s ";
                    document.getElementById("timer").classList.add("complete");
                } else if (game_state[0] == 0){
                    document.getElementById("timer").classList.remove("complete");
                    document.getElementById("timer").innerHTML = "";
                }

            //running score
            document.getElementById("score_zone").innerHTML = "Score: " + data["running_score"];
             
            var button_detail = ["blurb","blib"];

        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
        
    });
}}




