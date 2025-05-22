let countdownFunction = 0;
let oxygenFunction = 0;
var puzzle_questions = [];

$(document).ready(ajax_call);
$("body").on("click", ".submit_button", ajax_call);
$("body").on("click", ".cp_button", cp_explore);
let ajaxRunning = false;
function safeAjaxCall() {
    if (ajaxRunning) return; // Prevent overlapping calls
    ajaxRunning = true;
    ajax_call().always(() => {
        ajaxRunning = false;
    });;
}
setInterval(safeAjaxCall, 10000); // every 10 seconds

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
    ajaxRunning = true;
    if ($(this).hasClass('inactive') || $(this).hasClass('blocked')){
        console.log('inactive clicked');
    } else {console.log("button clicked / name update");
       
    var cp = $(this).attr('cp');
    var last_hit = $(this).attr('id');
    cp = cp || 0;
    last_hit = last_hit || "no id";


    var cp_option_choice = $(this).attr('cp_option_choice');
    cp_option_choice = cp_option_choice || 0;

    var user_input = "void";
    if($(this).hasClass('puzzle')){
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
   
    if (cp == 999){
        const userConfirmed = confirm("Are you sure you're ready to start/stop?")
        if(!userConfirmed) { return;};
    }

    //bit of jazz
    $(this).addClass('inactive');
    var temp_highlight = $("#cp"+cp);
    temp_highlight.addClass('clicked');
    
    setTimeout(function() {
        temp_highlight.removeClass('clicked');
    }, 2000);

   
    console.log('ajax fire');
    return $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'assets/php/test.php?cp_option_choice='+cp_option_choice+'&purpose=1&cp='+cp+'&user_input='+user_input,
        success: function(data) {
            console.log("ajax return");
            var debug_log = data["debug_log"];
            var db_response = data["db_response"];
            console.log(db_response);
            console.log(debug_log);
            var inventory = data["inventory"];
            var cp_bible = data["cp_bible"];
            var cp_keys = Object.keys(cp_bible);
            console.log(cp_bible);
            var comment = data["comment"];
            var teams = data["teams"];
            console.log(teams);
            var this_team   = data["this_team"];
            var stats = data ["stats"];
            sessionStorage.setItem('userStats', JSON.stringify(stats));
            console.log(stats);

            // showTemporaryMessage(comment, 3000);

            //inventories:
            console.log(inventory);

            function populateTable(obj) {
                const tableBody = document.querySelector("#inventoryTable tbody");
                tableBody.innerHTML = ""; // Clear previous content
    
                Object.entries(obj).forEach(([key, value]) => {
                    const row = document.createElement("tr");
                    row.innerHTML = `<td>${key}</td><td>${value}</td>`;
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
                alert ("Prize collected!");
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
                            if (cpx["available"]) {
                                rowId = "row"+key_id;
                               document.getElementById(rowId).style.display = 'block';
                              } else {
                                rowId = "row"+key_id;
                                document.getElementById(rowId).style.display = 'none';
                                console.log('hidden'+key_id);
                              };   
                                
                                //ALL ** THIS ** LOGIC ** SHOULD ** BE ** IN ** THE ** BACK ** END....!
                                //add CP options
                                var comment_space = `cp_comment_space_`+ this_key;

                                
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
                                    var this_option_id = 'cp'+this_key+'option'+key;
                                    if(last_hit == this_option_id){
                                        blocked = "inactive";
                                        console.log(blocked);
                                    } else {blocked = "active";}
                                    document.getElementById(target_space).innerHTML += '<button id="'+this_option_id+'" class="submit_button ' + puzzle_class + ' '+blocked+'" cp="' + this_key + '" cp_option_choice="'+ key +'">' + these_options[key] + '</button>';                
                                    });
                                    setTimeout(() => {
                                    document.getElementById(last_hit).classList.remove("inactive");
                                    document.getElementById(last_hit).classList.add("active");
                                     }, 3000);
                                
                                var cp_header_id = "cp-header-"+this_key;
                                document.getElementById(cp_header_id).innerHTML = cpx["name"];

                                
                                var cp_space = `cp_option_card_`+this_key;
                                const info_space = `cp_info_space_`+ this_key;
                                


                                document.getElementById(info_space).innerHTML = cpx["message"];
                                if(cpx["puzzle"]){
                                    document.getElementById(info_space).innerHTML += '<br><br>' + cpx["puzzle_q"];    
                                }
                                document.getElementById(cp_space).classList.add('neutral');
                            resolve(element); // Element found, resolve the promise

                        } else if (elapsedTime >= timeout) {
                            console.log("Timeout reached, stopping checks.");
                            reject(new Error("Element not found within timeout"));
                        } else {
                            console.log("Element "+cp_id+" not found, waiting 3 seconds...");
                            setTimeout(check, 250); // Wait for 3 seconds and recheck //THIS ISNT THE TIME TO FIX IT,,, but this is BAD! IT SETS ADDITONALY CHECK like CRZY!!
                        }
                    }
            
                    check(); // Start the checking process
                });
            }
                //iterate across checkpoitns
                console.log(cp_bible);
            for (let cp in cp_bible) {
                var this_cp = cp_bible[cp]; // Logs each object's details
                var cp_id = "butt" + this_cp["cp"];
                var cp_name = this_cp["name"];
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
                document.getElementById("teams").style.display = "none";
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
                document.getElementById("teams").style.display = "none";
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
           ajaxRunning = false;     
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
            ajaxRunning = false;
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
});



