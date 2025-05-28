<?PHP
session_start();

$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$game = $_SESSION['game'];
$debug = 1;
$teams_active = true;
$response = [];
$debug_log = [];
$debug_log[] = "data play";
$user_input = $_REQUEST["user_input"];
$incoming_cp = $cp;

//game specifics (to include in teams)

include 'cp_bible.php';
include 'puzzle_bible.php';

include 'db_connect.php';
include 'word_check.php';
include 'game_letters.php';
include 'valid_words.php';
include 'invalid_words.php';

    $scrabble_values = [
        'A' => 1,
        'B' => 3,
        'C' => 3,
        'D' => 2,
        'E' => 1,
        'F' => 4,
        'G' => 2,
        'H' => 4,
        'I' => 1,
        'J' => 8,
        'K' => 5,
        'L' => 1,
        'M' => 3,
        'N' => 1,
        'O' => 1,
        'P' => 3,
        'Q' => 10,
        'R' => 1,
        'S' => 1,
        'T' => 1,
        'U' => 1,
        'V' => 4,
        'W' => 4,
        'X' => 8,
        'Y' => 4,
        'Z' => 10,
        ' ' => 0,  // Blank tile has 0 points
    ];
    $word_length_value = [0,0,0,0,3,7,12,18];

$query_words = "select * from dbo.words";
$valid_words_array = [];
$invalid_words_array = [];
$stmt_words = $conn->prepare($query_words);
    $stmt_words->execute();
while ($db_word = $stmt_words->fetch(PDO::FETCH_ASSOC)) {
    if($db_word['valid']){
        $valid_words_array[] = $db_word['word'];
    } else {
        $invalid_words_array[] = $db_word['word'];
    }
}

//include custom php
    // e.g. include 'word_check.php';

ini_set("allow_url_fopen", 1); //this is important for fetching remote files

//Get event results from DB:

//test_game to be made into a variable
$query = "select * from dbo.test_game where location = $location AND game = $game ORDER BY Time_stamp ASC";

if($_REQUEST["purpose"] == 2){ //irrelevant as we need everything in a teams scenario
$query = "select * from dbo.test_game where location = $location AND game = $game ORDER BY Time_stamp ASC";
}

$result = $conn->query($query);

$query = "select * from dbo.test_game where game = :game and location = :location";
    $stmt = $conn->prepare($query);
    // Bind values to the placeholders
    $stmt->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt->execute();

$usernames = [];
$query2 = "select * from dbo.users";

//don't strictly always need all users. but haven't got a viable filter on this yet. This will come from the pre-game page
$stmt2 = $conn->prepare($query2);
    $stmt2->execute();
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $usernames[$row2['id']] = $row2['name'];
}

if($teams_active){
    $debug_log[]  = "44. teams active";
    //get all the teams
    $query3 = "select * from dbo.teams where game = :game and location = :location";
    $stmt3 = $conn->prepare($query3);
    // Bind values to the placeholders
    $stmt3->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt3->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt3->execute();
    while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
        $teams[$row3['UID']] = [
            "name" => $row3['name'],
            "members" => [],
            "cps" => [],
            "score" => 0,
            "params" => [
                "game" => [
                    "game_start" => 0,
                    "game_state" => 0,
                    "game_end" => 0
                ],
                "score" => 0,
                "commentary" => [],
                "level" => 0,
                "cp_bible" => $cp_bible,
                "current_word" => "",
                "current_word_score" => 0,
                "letter_bonus" => 1,
                "next_letter" => 0,
                "word_bonus" => 1,
                "used_bonus" => false,
                "used_words"=> []
            ],
            "stats" => [
                "words_played" => [],
                "puzzles" => ["attempts" => [], "solved"=>[]],
                "letters_played" => 0
            ]
        ];
    }

    //get all the team_members
    $query4 = "select * from dbo.team_members where game = :game and location = :location";
    $stmt4 = $conn->prepare($query4);
    // Bind values to the placeholders
    $stmt4->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt4->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt4->execute();
    $teamed_players = [];
    $players = [];
    while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
       $teams[$row4["team"]]["members"][] = $row4["player_ID"];
       $teamed_players[] = $row4["player_ID"];
       if($row4["player_ID"] == $user_ID){
        $this_team = $row4["team"];
       }
       //set-up the player
       $players[$row4["player_ID"]] = [ 
            "team" => $row4["team"],
            "name" => $usernames[$row4["player_ID"]],
            "params" => [ 
                "used_cps" => [],
                "puzzle_cooldown" => 0,
                "bonus" => [
                    "letter bonus" => 1,
                    "word bonus" => 1,
                ]
            ],
            "history" => [],
            "inventory" => [
                "letter bonus" => "-",
                "word bonus" => "-",
                "Current Word" => "-"
            ]
        ];
    $debug_log['player details'] = $players;
    }
}

$i = 0;

//build punches list
$player_cps = [];
$all_punches = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $all_punches[] = [$row["CP_ID"],$row["Time_stamp"],$row["puzzle_answer"],$row["Player_ID"],$players[$row["Player_ID"]]["team"],$row["cp_option"]]; //this has all punches now.
   $i += 1;
}
$debug_log[] = $all_punches;

//build the teams

$x = 0;

// GAME SPECIFIC
//set-up the static constants (each requires it's own rule...):

     //special CPS;
     $cp_start_finish = [998,999];


     //results catchers (don't change this, it's solid)
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    $available_cps = []; //which CPs are immediately available?
    $live_result = [];

    //values
    $hand_limit = 2;
    $stage_time = 900*60;
    $alert = 0;

//TEAM SPECIFIC catchers (customise the catchers here)

    if($user_ID == 29){
        $cp_bible[999]["available"] = true;
    }

    $pl_finishers=  [];

//PLAYER SPECIFIC customise $players here

    foreach($players as $player){
    }
    // for each player
    // history
    // inventory


//start looping the contestants:
foreach($teams as $team_UID => $team){

    if($_REQUEST["purpose"] != 45){
        if($team_UID != $this_team){
            $debug_log[] = "skipping $team_UID";
            continue; //skipping teams that aren't the active one
        }
    }

    $debug_log[] = "playing with $team_UID";
        //while($x < $count_results){
    /// not needed $team_UID = key($team);
    $name = $team["name"];
    $team_result = $team["cps"]; //$results[$x];
  // don't have this data yet...
    $surname = "data"; //update
    $finish_time = 0 ; //update - why is this here???
    $team_player_count = 0;
    $team_finish_count = 0;
    $x += 1;

    
if($debug == 1){ $debug_log[] = '72';};
//GAME SPECIFIC set-up course/result variables for each contestants
   // $this_cp_names = $cp_names;

    foreach($team["members"] as $team_member){
        // $available_cps[$team_member] = [999]; CPs are per team in this game
        $team_player_count += 1;
    }
}
    
//GENERIC player specific starting values
    $id = $x;
    $results_ids[] = $id;
    $results_names[$id] = [$name,$surname];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    //$commentary = [];
    $count_cps = count($all_punches);
    $y = 0;
    $running_score = 0;
    //$game_state = 0;
    //$game_start = 0;
    //$game_end = 0;
    $game_time = 0;
    $time_penalty = 0;
    $current_timezone = 0;


    // cycle through the punch list;
    $z = 0;
    
    while ($z < $count_cps){
 
        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp_number = intval($all_punches[$z][0]);
        $pl = intval($all_punches[$z][3]);
        $tm = intval($all_punches[$z][4]);
        $cp_option = intval($all_punches[$z][5]);
        $cp = $teams[$tm]["params"]["cp_bible"][$cp_number];// $cps[$z];
        $t = $all_punches[$z][1]; //times[$z];
        $puzzle_answer = strtolower($all_punches[$z][2]);
        $purp = $all_punches[$z][5];
        $debug_log['297'] = $all_punches[$z];
        $z += 1;
        $puzzle_response = 0;
        $alert = 0;
        $game_time = $t - $teams[$tm]["params"]["game"]["game_start"];

        if($game_time > $stage_time && $cp_number != 999 ){
            foreach ($cp_bible as $key => $cp) {
                    $teams[$tm]["params"]["cp_bible"][$key]['available'] = false;
                    }
                    $comment = "The game has ended.";
                }
                else {

        //Letter
        if($cp["type"] == "letter") {
            if($cp_option == 1){
                $play_letter = $teams[$tm]["params"]["cp_bible"][$cp_number]["value"];
                $next_letter = $game_letters[$teams[$tm]["params"]["next_letter"]];
                $teams[$tm]["params"]["next_letter"] += 1;
                $teams[$tm]["params"]["current_word"] .= $play_letter;
                $teams[$tm]["params"]["cp_bible"][21]["message"] = "Play <h1>".$teams[$tm]["params"]["current_word"]."</h1>";
                $teams[$tm]["params"]["cp_bible"][$cp_number]["name"] = $cp_number.' - <div class="scrabble-tile letter-tile">
                                                                        <span class="letter">'.$next_letter.'</span>
                                                                        <span class="points">'.$scrabble_values[$next_letter].'</span>
                                                                        </div>';
                $teams[$tm]["params"]["cp_bible"][$cp_number]["value"] = $next_letter;
                $teams[$tm]["params"]["current_word_score"] += $scrabble_values[$play_letter] * $teams[$tm]["params"]["letter_bonus"];
                $comment = "<em>".$play_letter."</em> played";
                $teams[$tm]["stats"]["letters_played"] += 1;

                if($teams[$tm]["params"]["letter_bonus"] > 1) {
                    $comment .= " with a ".$teams[$tm]["params"]["letter_bonus"]."x bonus";
                }
                $teams[$tm]["params"]["letter_bonus"] = 1;
            }
            //use a bonus
            if($cp_option == 2){
                if($teams[$tm]["params"]["used_bonus"] == false){
                $played_letter_bonus = $players[$pl]["inventory"]["letter bonus"];
                if($played_letter_bonus > 1){
                    $teams[$tm]["params"]["letter_bonus"] = $played_letter_bonus;
                    $teams[$tm]["params"]["used_bonus"] = true;
                    $comment = $played_letter_bonus." letter bonus played";
                    $players[$pl]["params"]["bonus"]["letter bonus"] = 1;
                    $players[$pl]["inventory"]["letter bonus"] = "-";
                } else {
                    $comment = "no letter bonus held";
                }
           } else {
            $comment = "This word already has a bonus applied";
           }
        }
            //play a word bonus
            if($cp_option == 3){
                if($teams[$tm]["params"]["used_bonus"] == false){
                $played_word_bonus = $players[$pl]["params"]["bonus"]["word bonus"];
                if($played_word_bonus > 1){
                    $teams[$tm]["params"]["word_bonus"] = $played_word_bonus;
                    $teams[$tm]["params"]["used_bonus"] = true;
                    $comment = $played_word_bonus."x word bonus played";
                    $players[$pl]["params"]["bonus"]["word bonus"] = 1;
                    $players[$pl]["inventory"]["word bonus"] = "-";
                } else {
                    $comment = "no word bonus held";
                }
            } else {
                $comment = "This word already has a bonus applied";
            } 
        }
    }

        //puzzle point
        if($cp["type"] == "puzzle point"){
            //solve puzzle to pick-up the bonus
            if($cp["available"]){
                if($players[$pl]["params"]["puzzle_cooldown"] + 30 > $t){
                    // wait for the cool down bro...
                    $wait_left = $players[$pl]["params"]["puzzle_cooldown"] + 30 - $t + 5;
                    $players[$pl]["params"]["puzzle_cooldown"] += 5;
                    $comment = "Puzzle locked for $wait_left seconds<br>(5s added)";
                } else {

            $teams[$tm]["stats"]["puzzles"]["attempts"][] = $cp["name"];

            if($puzzle_answer == $cp["puzzle_a"]){
            $teams[$tm]["stats"]["puzzles"]["solved"][] = $cp["name"];
                //word puzzle
                if($cp["bonus"]["type"] == "word"){
                    if($players[$pl]["params"]["bonus"]["word bonus"] == 1) {
                        $players[$pl]["inventory"]["word bonus"] = $cp["bonus"]["value"]."x";
                        $players[$pl]["params"]["bonus"]["word bonus"] = $cp["bonus"]["value"];
                        $comment = $cp["bonus"]["value"]."x ".$cp["bonus"]["type"]." bonus collected";
                        $teams[$tm]["params"]["cp_bible"][$cp_number]["available"] = false; 
                    } else {
                        $comment = $cp["bonus"]["type"]." bonus already held";
                    }
                } elseif ($cp["bonus"]["type"] == "letter") {

                    if($players[$pl]["params"]["bonus"]["letter bonus"] == 1) {
                        $players[$pl]["inventory"]["letter bonus"] = $cp["bonus"]["value"]."x";
                        $players[$pl]["params"]["bonus"]["letter bonus"] = $cp["bonus"]["value"]."x";
                        $comment = $cp["bonus"]["value"]."x ".$cp["bonus"]["type"]." bonus collected";
                        $teams[$tm]["params"]["cp_bible"][$cp_number]["available"] = false; 
                    } else {
                        $comment = $cp["bonus"]["type"]." bonus already held";
                    }
                }
            } else {
                $comment = "Incorrect.<br>Puzzle locked for 30s";
                $players[$pl]["params"]["puzzle_cooldown"] = $t;
            }}}
            else {
                $comment = "puzzle already solved";
            }
        }

        //Play point
        if($cp["type"] == "wsf"){
            //enter word
            $current_word = $teams[$tm]["params"]["current_word"];
            $comment = "got here";
            $valid = false;
            if(in_array($current_word,$valid_words_array)){
                $valid = true;
            } elseif (in_array($current_word,$invalid_words_array)){
                $valid = false;
            } else {
                $valid = isValidEnglishWord($current_word);
                $debug_log["word check"] = $current_word." checked on the api";
                $stmt = $conn->prepare("INSERT INTO words (word, valid) VALUES (:word, :valid)");
                $stmt->bindParam(':word', $current_word);
                $stmt->bindParam(':valid', $valid);
                $stmt->execute();
            }
            if($valid){
                $word_length = strlen($current_word);
                if($word_length > 7){ $word_length = 7;}
                if($word_length > 2){
                if(in_array($current_word,$teams[$this_team]["params"]["used_words"])){
                $valid_words_array[] = $current_word;
                $comment = "$current_word played, already used.";
                } else {

                $value = $word_length_value[$word_length] + ($teams[$tm]["params"]["current_word_score"] * $teams[$tm]["params"]["word_bonus"]);
               $teams[$tm]["params"]["score"] += $value;
                $used_words[] = $current_word;
                $comment = "$current_word successfully played! for $value points";
                $teams[$tm]["stats"]["words_played"][] = ["word" => $current_word, "score" => $value];
                }}
                else {
                    $comment = "word too short";
                }
            } else {
                $invalid_words_array[] = $current_word;
                $comment = "$current_word played, but not a known word";
            }
            $teams[$tm]["params"]["word_bonus"] = 1;
            $teams[$tm]["params"]["used_bonus"] = false;
            $teams[$tm]["params"]["current_word"] = "";
            $teams[$tm]["params"]["current_word_score"] = 0;
            $value = 0;
            $teams[$tm]["params"]["cp_bible"][21]["message"] = "Current word empty";
        
        }

        //start_finish
        $debug_log[]  = "cp_type = ".$cp['type'];
        if($cp['type'] == "start_finish"){
            if($cp_number == 999){
            if($teams[$tm]["params"]["game"]["game_state"] == 0)
            {
                //require 'start_game.php';

                $teams[$tm]["params"]["game"]["game_state"] = 1;
                $teams[$tm]["params"]["game"]["game_start"] = $t;
                $debug_log[]  = "game state =" . $teams[$tm]["params"]["game"]["game_state"];
                    foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                        $checkpoint["available"] = true;
                    }
                    unset($checkpoint);
                    $teams[$tm]["params"]["cp_bible"][999]["available"] = false;
                    $teams[$tm]["params"]["cp_bible"][999]["options"] = [];
                    $comment = "game started.";
                
            } 
            elseif ($teams[$tm]["params"]["game"]["game_state"] == 4) { //taking this out of operation
               //$game_state = 0;
               //$game_start = 0;
               //$game_end = 0;
               $teams[$tm]["params"]["game"]["game_state"] = 0;
               $teams[$tm]["params"]["game"]["game_start"] = 0;
               $teams[$tm]["params"]["game"]["game_end"] = 0;
               $comment = "game reset";
           } }
            elseif
            ($cp_number == 998){
                if(in_array($pl,$pl_finishers)){
                    $comment = "already finished";
                } elseif ($game_time >= $stage_time ) {
                    $comment = "too late to finish";
                } else {
                    $pl_finishers[] = $pl;
                    $finish_bonus = 50/(count($teams[$tm]["members"]));
                        $teams[$tm]["params"]["score"] += $finish_bonus;
                        unset($checkpoint);
                        $comment = "Finished. Bonus: $finish_bonus";
                    if($pl == $user_ID){
                        foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                            $checkpoint["available"] = false;
                        } 
                        unset($checkpoint);
                    }
                }
            }
        }
        //
    }

        //ONCE THE CP ACTION HAS BEEN TAKEN:
        $teams[$tm]["params"]["commentary"][] = "Player ".$pl." - ".$comment;
        $results_detailed[$id][] = [$t,$cp_number,$comment,"",$running_score];
    }

    //ONCE WE HAVE CYCLED THROUGH THE CPs..

    $final_score = $running_score - $time_penalty;
    $time = $game_time;
       //live results
        //$live_result[$name]=$final_score;
        //$results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
   

$final_results = [];

foreach ($teams as $team) {
    if (isset($team['name']) && isset($team['score'])) {
        $final_results[$team['name']] = $team['score'] + $team['params']["score"];
    }
}

$debug_log[] = $final_results;
$debug_log[] = $teams;

//CHOOSE WHAT TO ISSUE BACK, BASED ON PORPOISE

if($_REQUEST["purpose"] !== 2){
    //GAME SPECIFIC
    //UNIVERSAL
$response["cp_bible"]= $teams[$this_team]["params"]["cp_bible"]; //available_cps[$user_ID]; THIS NEEDS TO BE UPDATED!!!
//don't send back a puzzle response if nothing has been submitted.
if($incoming_cp > 0) {
$response["puzzle_response"]=$puzzle_response;
$response["comment"] = $comment;}
$response["running_score"] = $running_score;
$response["alert"] = $alert;
$response["this_team"] = $this_team;
$response["usernames"] = $usernames;
$response["game_state"] = [$teams[$this_team]["params"]["game"]["game_state"],$teams[$this_team]["params"]["game"]["game_start"],$teams[$this_team]["params"]["game"]["game_end"],$stage_time];
$players[$user_ID]["inventory"]["Current Word"] = $teams[$this_team]["params"]["current_word"];
$response["inventory"] = $players[$user_ID]["inventory"];
}
$response["teams"] = $teams;
$response["stats"] = $teams[$this_team]["stats"];
$response["live_scores"] = $final_results;
$response["commentary"] = $teams[$this_team]["params"]["commentary"];
$response["debug_log"] = $debug_log;
$response["db_response"] = $db_response;
echo json_encode($response);