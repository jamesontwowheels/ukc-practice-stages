let countdownFunction = 0;
let oxygenFunction = 0;
let mistouch = false;
var puzzle_questions = [];

$(document).ready(ajax_call);
$("body").on("click", ".submit_button", ajax_call);
$("body").on("click", ".cp_button", cp_explore);


function pause(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showTemporaryMessage(message, duration) {
    const tempMessage = document.getElementById('tempMessage');
    
    // Set the message
    tempMessage.textContent = message;
    console.log('message');
    
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
     if(mistouch)
        { console.log('mistouch');
            return; }
    
    var cp = $(this).attr('cp');
    cp = cp || 0;

    
    var cp_option_choice = $(this).attr('cp_option_choice');
    cp_option_choice = cp_option_choice || 0;

    console.log("cp_option = " + cp_option_choice);
    if(cp_option_choice > 0){
        mistouch = true;
        setTimeout(() => {
            mistouch = false;
        }, 500);
    }

    var user_input = "void";
    if($(this).hasClass('puzzle') && cp_option_choice == 1){
        user_input = prompt("submit your answer");
        if (user_input === null || user_input == "") { 
            console.log("nothing input");
            return;}
    } else{ console.log("no puzzle"); }

    var cp = $(this).attr('cp');
    cp = Number.isInteger(Number(cp)) ? Number(cp) : 0;
    
    if ($(this).hasClass('cp_button')){
        cp = 0;
    }
   
    //bit of jazz
    $(this).addClass('blocked');
    var temp_highlight = $("#cp"+cp);
    temp_highlight.addClass('clicked');
    
    setTimeout(function() {
        temp_highlight.removeClass('clicked');
    }, 2000);

   
    console.log('ajax fire');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'assets/php/test.php?cp_option_choice='+cp_option_choice+'&purpose=1&cp='+cp+'&user_input='+user_input,
        success: function(data) {
            console.log("ajax return");
            var debug_log = data["debug_log"];
            var db_response = data["db_response"];
            console.log(db_response);
            console.log(debug_log);
            var detailed_results = data["detailed_results"];
            console.log(detailed_results);
            var inventory = data["inventory"];
            var cp_bible = data["cp_bible"];
            var cp_keys = Object.keys(cp_bible);
            console.log(cp_bible);
            var comment = data["comment"];
            var teams = data["teams"];
            console.log(teams);
            var this_team   = data["this_team"];
            var animation = data["animation"] ?? [false];
            console.log (animation);

            if(animation[0]) {
                const overlay = document.getElementById("overlay");
                overlay.classList.add("active");
                // Keep it on screen for 3 seconds, then fade out
                    setTimeout(() => {
                        overlay.classList.remove("active");
                    }, 3000);
                }
            // showTemporaryMessage(comment, 3000);

            //inventories:
            console.log(inventory);

            function populateTable(obj) {
                const tableBody = document.querySelector("#inventoryTable tbody");
                tableBody.innerHTML = ""; // Clear previous content
    
                Object.entries(obj).forEach(([key, value]) => {
                    const row = document.createElement("tr");    
                    let displayValue = Array.isArray(value)
                        ? value.join(", ")        // <-- space between items
                        : value;
                    row.innerHTML = `<td>${key}</td><td>${displayValue}</td>`;
                    tableBody.appendChild(row);
                });
            }

            populateTable(inventory);

            //puzzle response
            var puzzle_response = data["puzzle_response"];
            if(puzzle_response == 1){
                alert("Yes! Puzzle correct");
            } else if(puzzle_response == 2){
                alert ("No. Puzzle incorrect");
            } else if (puzzle_response == 3) {
                alert ("Prize collected!"); //Is this a common response?
            }

            // checkpoint function
            function checkElementExists(id, key_id, keyname, cpx, timeout = 15000) {
                return new Promise((resolve, reject) => {
                    const startTime = Date.now();
            
                    // Recursive function to check for the element
                    function check() {
                        const elapsedTime = Date.now() - startTime;
                        const element = document.getElementById(id);
            
                        if (element) {
                            element.innerHTML = keyname;
                            element.classList.remove("blocked");
                            element.classList.remove("puzzle");
                            var opt_card = "cp_option_card_"+key_id;
                            var rowId = "row"+key_id;
                            if (cpx["available"]) {
                                document.getElementById(opt_card).classList.add("available");
                               document.getElementById(rowId).style.display = 'block';
                              } else {
                                document.getElementById(opt_card).classList.remove("available");
                                document.getElementById(rowId).style.display = 'none';
                              };   
                                
                                //ALL ** THIS ** LOGIC ** SHOULD ** BE ** IN ** THE ** BACK ** END....!
                                //add CP options
                                var comment_space = `cp_comment_space_`+ this_key;
                                //ENHANCEMENT: Add colour coding for good/bad/neutral comments

                                
                                if (typeof comment === "string" && comment.length > 0) {
                                    console.log("String is not empty!");
                                    document.getElementById(comment_space).innerHTML = comment;
                                    document.getElementById(comment_space).classList.add('cp_comment_filled');
                                } else {
                                    document.getElementById(comment_space).innerHTML = "";
                                    document.getElementById(comment_space).classList.remove('cp_comment_filled');
                                }

                                const target_space = `cp_option_space_` + this_key;
                                var these_options = cpx["options"];   
                                document.getElementById(target_space).innerHTML = "";
                                let itemDiv = document.getElementById(target_space);          
                                Object.keys(these_options).forEach(key => {
                                    var puzzle_class = "";
                                    if(cpx["puzzle"]){
                                        puzzle_class = "puzzle";
                                    }
                                    document.getElementById(target_space).innerHTML += '<button id="optionbutton'+this_key+key+'" class="submit_button active ' + puzzle_class + '" cp="' + this_key + '" cp_option_choice="'+ key +'">' + these_options[key] + '</button>';                
                                    });
                                
                                var cp_header_id = "cp-header-"+this_key;
                                document.getElementById(cp_header_id).innerHTML = cpx["name"];

                                
                                var cp_space = `cp_option_card_`+this_key;
                                const info_space = `cp_info_space_`+ this_key;
                                


                                document.getElementById(info_space).innerHTML = cpx["message"];
                                if(cpx["puzzle"]){
                                    document.getElementById(info_space).innerHTML += '<br><br>' + cpx["puzzle_q"];    
                                }

                                //add an image:
                                if(cpx["image"][0] == 1){
                                    document.getElementById(info_space).innerHTML += '<img class="puzzle_pic" src="assets/img/' + cpx["image"][1] + '">';
                                }

                                //add a 15-puzzle:
                                if(cpx["15-puzzle"] == true){
                                    console.log(cpx["image"][1]);
                                    const puzzle_space = `15_puzzle`+this_key;
                                    const puzzle_button_id = "optionbutton"+this_key+"1";
                                    document.getElementById(puzzle_button_id).classList.add('inactive');
                                    document.getElementById(info_space).innerHTML += "<div id='"+puzzle_space+"'></div>";
                                    var container15 = document.getElementById(puzzle_space);
                                    initImagePuzzle(container15, 'assets/img/jester.png',puzzle_button_id)
                                }

                                //add a dragon-game:
                                if(cpx["blink-game"] == true){
                                    const blink_space = `blink_puzzle`+this_key;
                                    const blink_button_id = "optionbutton"+this_key+"1";
                                    document.getElementById(blink_button_id).classList.add('inactive');
                                    document.getElementById(info_space).innerHTML += "<div id='"+blink_space+"'></div>";
                                    startDragonGame(blink_space,blink_button_id);
                                }

                                document.getElementById(cp_space).classList.add('neutral');
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
                //iterate across checkpoitns
            for (let cp in cp_bible) {
                var this_cp = cp_bible[cp]; // Logs each object's details
                var cp_id = "butt" + this_cp["cp"];
                var emoji = this_cp && this_cp.emoji ? "&#" + this_cp.emoji + "; " : "";
                var cp_name = emoji + this_cp["name"];
                var this_key = this_cp["cp"];
                
                checkElementExists(cp_id, this_key, cp_name, this_cp, 15000).then( function() {
                    console.log("trying to add " + this_cp["name"])
                    //document.getElementById(cp_id).innerHTML = cp_names[this_key];
                }).catch(error => {
                    console.error(error.message);
                });
            }

            
         
            //alert feedback
            var alert_response = data["alert"];
            if (alert_response != 0) {
                alert(alert_response);
            }

            //game state
            var game_state = data["game_state"];
            console.log(game_state);
            var game_start = parseInt(game_state[1]);
            var game_end = parseInt(game_state[2]);
            var stage_time = game_state[3];
            if(game_state[0] == 1){
                // document.getElementById("teams").style.display = "none";
                    // Set the date and time we're counting down to
                const countdownDate = game_start + stage_time; // 5 minutes from now
                // Update the countdown every second            
                    clearInterval(countdownFunction);
                    countdownFunction = setInterval(function() {
                    // Get the current date and time
                    const now = new Date().getTime();
                    const now_s = Math.floor(now/1000);
                    // Calculate the time difference between now and the countdown date
                    const distance = countdownDate - now_s;

                    // Time calculations for minutes and seconds
                    var minutes = Math.floor((distance) / (60));
                    var seconds = Math.floor(distance % (60));
                    
                    console.log('timer update');

                    // Display the result in the element with id="timer"
                    document.getElementById("timer").innerHTML = minutes + "m " + seconds + "s ";
                },1000)} else if (game_state[0] == 2){
               // document.getElementById("teams").style.display = "none";
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
            console.log(data["teams"][this_team]["params"]);
            document.getElementById("score_zone").innerHTML = "<b>Score</b>: " + data["teams"][this_team]["params"]["score"];
             
            var button_detail = ["blurb","blib"];
        
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
                    const bodyElement = document.getElementById("footer");

                    // Time calculations for minutes and seconds
                    const o2_minutes = Math.floor((o2_distance) / (60));
                    const o2_seconds = Math.floor(o2_distance % (60));
                    document.getElementById("o2_timer").innerHTML = "Oxygen Timer:" + o2_minutes + "m " + o2_seconds + "s ";                 
                    document.getElementById("water").classList.add("underwater");
                    // Select all bubbles
                    const divs = document.querySelectorAll('div.bubble1');
                    // Make them bubbly"
                    divs.forEach(function(div) {
                        div.classList.add('bubble');
                        div.classList.remove('snowflake');
                    });
                    if (o2_distance < 1) {
                        clearInterval(oxygenFunction); 
                        document.getElementById("o2_timer").innerHTML = "Out of oxygen";
                        // Stop the time
                        // Add the pulse-red class to the body to trigger the pulsing effect
                        bodyElement.classList.add('full-red');
                    } else if (o2_distance < 180) {
                        bodyElement.classList.add('rapid-red');
                    } else if (o2_distance < 330){
                        bodyElement.classList.add('pulse-red');
                    } else {
                        bodyElement.classList.add('initial-red')
                    };

                },1000)
            }} else {
                clearInterval(oxygenFunction);
                oxygenFunction = 0;
                document.getElementById("o2_timer").innerHTML = ""; 
                document.getElementById("water").classList.remove("underwater");
                document.getElementById("footer").classList.remove("rapid-red");
                document.getElementById("footer").classList.remove("full-red");
                console.log('this hsould be removing the full red');
                document.getElementById("footer").classList.remove("pulse-red");
                document.getElementById("footer").classList.remove("initial-red");
                // Select all bubbles
                const divs = document.querySelectorAll('div.bubble1');
                // Make them bubbly"
                divs.forEach(function(div) {
                    div.classList.remove('bubble');
                    div.classList.add('snowflake');
                });
            }

        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
        }
        
    });
}}

function cp_explore() {
    if (this.classList.contains("active")) {
        // Proceed with the desired action
        var cp = $(this).attr('cp');
        var cp_option_id = 'cp_option_card_' + cp;
        var comment_space = 'cp_comment_space_' + cp;
        document.getElementById(comment_space).innerHTML = "";
        document.getElementById(comment_space).classList.remove("cp_comment_filled");
        document.getElementById(cp_option_id).classList.add("cp-option-show");
        document.getElementById(cp_option_id).classList.remove("cp-option");
      } else {
        // Optional: Prevent the default action or show a warning
        console.log("Button is not active, action blocked.");
      }
}

$(document).on('click', '.close-btn', function () {
    const $parentCard = $(this).parent(); // Get the parent card
    $parentCard.removeClass('cp-option-show'); // Remove the "show" class
    $parentCard.addClass('cp-option'); // Add the "cp-options" class
    $parentCard.removeClass("show_first");
});



