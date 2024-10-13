let countdownFunction = 0;
let oxygenFunction = 0;
var puzzle_questions = [];

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
    
    var user_input = "void";
    if($(this).hasClass('puzzle')){
        user_input = prompt(puzzle_questions[cp]);
        if (user_input == "void" || user_input == "") { 
            console.log("nothing input");
            return;}
    } else{ console.log("no puzzle"); }

    var cp = $(this).attr('cp');
    cp = cp || 0;
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
            document.getElementById("inventory_zone").innerHTML = "Current word =" + data["current_word"] + "<br> Current bonus = " + data["current_bonus"];
            var cp_names = data["cp_names"];
            var cp_names_keys = Object.keys(cp_names);
            var available_cps = data["available_cps"];
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
                            element.classList.remove("blocked");
                            if (available_cps.includes(id)) {
                                console.log(id + ' is available.');
                                rowId = "row"+id;
                               document.getElementById(rowId).style.display = 'block';
                              } else {
                                console.log(id + ' is not available.');
                                rowId = "row"+id;
                                document.getElementById(rowId).style.display = 'none';
                              }
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
            
            //available CPs /// i think this needs to move into the other function where we're checkign whether the rows exist or not first... maybe it all does?
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

            //puzzle CPs
            var puzzle_cps = data["puzzle_cps"];
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

            //puzzle questions
            puzzle_questions = data["puzzle_questions"];
            
            //oxygen state
            var oxygen_state = data["oxygen_state"];
            console.log(oxygen_state);
            if(oxygen_state[0] == 1) {//oxygen in play
            if(oxygenFunction == 0){
            
                const oxygen_end = oxygen_state[1];
                oxygenFunction = setInterval(function(){
                    const o2_now = new Date().getTime();
                    const o2_now_s = Math.floor(o2_now/1000);
                    // Calculate the time difference between now and the countdown date
                    const o2_distance = oxygen_end - o2_now_s;
                    const bodyElement = document.getElementById("main");

                    // Time calculations for minutes and seconds
                    const o2_minutes = Math.floor((o2_distance) / (60));
                    const o2_seconds = Math.floor(o2_distance % (60));
                    document.getElementById("o2_timer").innerHTML = o2_minutes + "m " + o2_seconds + "s ";                 
                    document.getElementById("water").classList.add("underwater");
                    // Select all bubbles
                    const divs = document.querySelectorAll('div.bubble1');
                    // Make them bubbly"
                    divs.forEach(function(div) {
                        div.classList.add('bubble');
                        div.classList.remove('snowflake');
                    });
                    if (o2_distance < 1) {
                        clearInterval(oxygenFunction); // Stop the time
                        // Add the pulse-red class to the body to trigger the pulsing effect
                        bodyElement.classList.add('full-red');
                    } else if (o2_distance < 30) {
                        bodyElement.classList.add('rapid-red');
                    } else if (o2_distance < 60){
                        bodyElement.classList.add('pulse-red');
                    };

                },1000)
            }} else {
                clearInterval(oxygenFunction);
                oxygenFunction = 0;
                document.getElementById("o2_timer").innerHTML = ""; 
                document.getElementById("water").classList.remove("underwater");
                document.getElementById("main").classList.remove("rapid-red");
                document.getElementById("main").classList.remove("full-red");
                document.getElementById("main").classList.remove("pulse-red");
                // Select all bubbles
                const divs = document.querySelectorAll('div.bubble1');
                // Make them bubbly"
                divs.forEach(function(div) {
                    div.classList.remove('bubble');
                    div.classList.add('snowflake');
                });
            }

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
                    const minutes = Math.floor((distance) / (60));
                    const seconds = Math.floor(distance % (60));

                    // Display the result in the element with id="timer"
                    document.getElementById("timer").innerHTML = minutes + "m " + seconds + "s ";
                },1000)} else if (game_state[0] == 2){

                console.log ("timer cancelled! Interval = " + countdownFunction);
                    clearInterval(countdownFunction);

                    
                    const finish_time = stage_time - (game_end - game_start);
                    console.log(finish_time);
                    console.log("game ended");
                    const minutes = Math.floor((finish_time) / (60));
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



