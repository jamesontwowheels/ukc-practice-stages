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

$drone_routes = [ 
    1 => [[1,3,6],[0,0,0]],
    2 => [[1,4,6],[0,0,0]],
    3 => [[1,4,7],[0,0,0]],
    4 => [[2,4,6],[0,0,0]],
    5 => [[2,4,7],[0,0,0]],
    6 => [[2,5,7],[0,0,0]]
];

$train_params = [
    "engine" => [1200,900,720],
    "carriages" => [100,150,200],
    "science" => [0.1,0.3]
];

include 'cp_bible.php';
include 'puzzle_bible.php';

include 'db_connect.php';

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
                "drones" => [],
                "horses" => [],
                "drone_times" => [],
                "drone_gold" => 0,
                "drone_routes" => $drone_routes,
                "next_horse_ready" => 0,
                "ranch_horses" => [],
                "train" => ["engine" => 0,
                            "carriages" => 0,
                            "science" => 0,
                            "route" => [
                                "arrival" => 0,
                                "gold" => 0]]
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
            "params" => [ "used_cps" => []],
            "hand" => 0,
            "history" => [],
            "inventory" => [
                "Gold" => 0,
                "Tame horses" => 0,
                "Wild horses" => 0
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
    $stage_time = 90*60;
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
                    $comment = "The game has ended.";
                    }
                }
                else {

        //Wild Horses
        if($cp["type"] == "horse") {
            if(in_array( $cp_number,$teams[$tm]["params"]["horses"])){
                $comment = "Horse already collected";
            } else {
            $teams[$tm]["params"]["horses"][] = $cp_number;
            if($cp["cp"] == 13) {
                $players[$pl]["inventory"]["Wild horses"] = 0;
                $players[$pl]["inventory"]["Gold"] = 0;
                $comment = "Stand and deliver!";
                $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = "Oh no! You have tried to steal the bandit's horse and ended up losing your gold and wild horses";
                $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = [];
                $teams[$tm]["params"]["cp_bible"][$cp_number]["available"] = false;
        
            } else {
                $players[$pl]["inventory"]["Wild horses"] += 1;
                $comment = "Horse lassoed!";
                $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = "You have successfully collected this horse";
                $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = [];
                $teams[$tm]["params"]["cp_bible"][$cp_number]["available"] = false;
            }
        }
        }

        //Ranch
        if($cp['type'] == "ranch"){
            if($cp_option == 1){
                //drop wild horses
                $whs = $players[$pl]["inventory"]["Wild horses"];
                // Extract all the first elements
                $horse_readies = array_column($teams[$tm]["params"]["ranch_horses"], 0);
                // Get the maximum
                $next_horse_ready = max($horse_readies);
                if($whs>0){
                for ($i = 0; $i < $whs; $i++) {
                    $horse_ready = max($next_horse_ready + 120, $t + 120);
                    $next_horse_ready = $horse_ready;
                    $teams[$tm]["params"]["ranch_horses"][] = [$horse_ready,0];
                }
                $comment = "$whs wild horses left at the ranch";
                $players[$pl]["inventory"]["Wild horses"] = 0;
                } else {
                    $comment = "No wild horses to leave";
                }
            } elseif ($cp_option == 2){
                $new_horses = 0;
                $horses_in_training = [];
                foreach ($teams[$tm]["params"]["ranch_horses"] as &$time) {
                    if ($time[0] < $t && $time[1] == 0) {
                        $new_horses++;
                        $time[1] = 1;
                    } elseif ($time[0] >= $t && $time[1] == 0){
                        $horses_in_training[] = $time[0] - $t; 
                    }
                }
                unset($time);
                if($new_horses > 0){
                $players[$pl]["inventory"]["Tame horses"] += $new_horses;
                $comment = "$new_horses trained horses collected.";
                } else { $comment = "No horses to collect.";}
                if(count($horses_in_training) > 0){
                    $count_horses_in_training = count($horses_in_training);
                    $comment .= " $count_horses_in_training horses still in training";
                }
            }
        }

        //Drones
        if($cp['type'] == "drone"){
            if(in_array($cp_number,$teams[$tm]["params"]["drones"])) {
                $comment = "Drone point already activated";
            } else {
            if($puzzle_answer == $cp["puzzle_a"]){
            $current_level = $teams[$tm]["params"]["level"];
            $teams[$tm]["params"]["drones"][] = $cp_number;
            $teams[$tm]["params"]["cp_bible"][$cp_number]["message"] = "Drone point activated at Level $current_level";
            $teams[$tm]["params"]["cp_bible"][$cp_number]["puzzle"] = false;
            $teams[$tm]["params"]["cp_bible"][$cp_number]["available"] = false;
            $teams[$tm]["params"]["cp_bible"][$cp_number]["options"] = [];
            $comment = "Correct! Drone point activated at Level $current_level";

            //WE ARE MOVI@NG THE CP_BIBLE INTO PARAMS!!! ALL TEAM SPECIFIC REFERENCES NEED TO BE UPDATED, KAPICHE?

            //check on routes
            
            foreach ($teams[$tm]["params"]["drone_routes"] as &$entry) {
                if($entry[1][$current_level] == 0){
                    $route = $entry[0];
                    $isValid = true; 
                    foreach ($route as $node) {
                        if (!in_array($node, $teams[$tm]["params"]["drones"])) {
                            $isValid = false;
                            break;
                        }
                    }
                if ($isValid) {
                    $entry[1][$current_level] = 1;
                    $teams[$tm]["params"]["drone_times"][] = $t;
                }
                }
            }
            unset($entry);

            $active_drones = count($teams[$tm]["params"]["drone_times"]);
            $teams[$tm]["params"]["cp_bible"][26]["message"] = $active_drones.' active drones';

            //check on level-up
            if(count($teams[$tm]["params"]["drones"]) == 7) {
                $teams[$tm]["params"]["level"] += 1;
                $new_level = $teams[$tm]["params"]["level"];
                $old_level = $new_level - 1;
                $comment = "<br> Level $old_level complete";
                if($teams[$tm]["params"]["level"] < 3){
                    //level-up
                    $comment .= ", Level $new_level unlocked";
                    foreach ($cp_bible as $key => $cp) {
                        if (isset($cp['type']) && $cp['type'] === 'drone') {
                            $teams[$tm]["params"]["cp_bible"][$key]['puzzle_q'] = $puzzle_bible[$key][$new_level][0];
                            $teams[$tm]["params"]["cp_bible"][$key]['puzzle_a'] = $puzzle_bible[$key][$new_level][1];
                            $teams[$tm]["params"]["cp_bible"][$key]['puzzle'] = true;
                            $teams[$tm]["params"]["cp_bible"][$key]['message'] = "";
                            $teams[$tm]["params"]["cp_bible"][$key]['available'] = true;
                            $teams[$tm]["params"]["cp_bible"][$key]["options"][1] = "solve";
                        }
                    }
                    $teams[$tm]["params"]["drones"] = [];
                }
            }
        } else {
            $comment = "Incorrect";
        };
    }}
        //Drone station

        if($cp["type"] == "drone_base"){
            //calculate gold earned
            $drone_gold = 0;
            foreach($teams[$tm]["params"]["drone_times"] as $times){
                $add_gold = floor(($t - $times)/300); // if it's every 2 minutes
                $drone_gold += $add_gold;
            }
            //subtract gold already collected
                $drone_gold_collected = $teams[$tm]["params"]["drone_gold"];
                $delta_drone_gold = $drone_gold - $drone_gold_collected; 

            //collect remaining gold
            //$players[$pl]["inventory"]["Gold"] += $delta_drone_gold;
            $teams[$tm]["params"]["drone_gold"] += $delta_drone_gold;
            $teams[$tm]["params"]["score"] += $delta_drone_gold;
            $comment = "You banked ".$delta_drone_gold."kg of gold";
        }

        //Mine
        if($cp["type"] == "mine") {
            $gold_capacity  = 2 + $players[$pl]["inventory"]["Tame horses"] * 3;
            if($players[$pl]["inventory"]["Gold"] < $gold_capacity){
                $mine_gold_collected = $gold_capacity - $players[$pl]["inventory"]["Gold"];
                $players[$pl]["inventory"]["Gold"] = $gold_capacity;
                $comment = $mine_gold_collected."kg gold collected";
            } else {
                $comment = "You can't carry any more gold";
            }
        }

        //Bank
        if($cp["type"] == "bank"){
            if($players[$pl]["inventory"]["Gold"]>0){
                $gold_deposit = $players[$pl]["inventory"]["Gold"];
                $teams[$tm]["params"]["score"] += $gold_deposit;
                $players[$pl]["inventory"]["Gold"] = 0;
                $comment = $gold_deposit."kg of gold deposited";
            } else {
                $comment = "No gold available to deposit";
            }
        }

        //Train station
        if($cp["type"] == "station"){
            
            //check train is in the station
            if($teams[$tm]["params"]["train"]["route"]["arrival"] < $t){
                //unload
                    if($teams[$tm]["params"]["train"]["route"]["gold"] > 0){
                        $platform_time = $t - $teams[$tm]["params"]["train"]["routes"]["arrival"];
                        $platform_loss = min(floor($platform_time/60),10);
                        $bandit_tax = 1 - $platform_loss/10;
                        $gold_recovered = $teams[$tm]["params"]["train"]["routes"]["gold"] * $bandit_tax;

                        $teams[$tm]["params"]["train"]["routes"]["gold"] = 0;
                        $teams[$tm]["params"]["score"] += $gold_recovered;
                        $comment = $gold_recovered."kg gold banked";
                    } else {
                        $comment = "There's no gold left on the train.";
                    }
                    //set-off
                    if($cp_option == 2){
                        $next_train = $t + $train_params["engine"][$teams[$tm]["params"]["train"]["engine"]];
                        $gold_weight = $train_params["carriages"][$teams[$tm]["params"]["train"]["carriages"]];
                        $gold_purity = $train_params["science"][$teams[$tm]["params"]["train"]["science"]];
                        $arrival_gold = $gold_weight * $gold_purity;
                        $teams[$tm]["params"]["train"]["routes"]["gold"] = $arrival_gold;
                        $teams[$tm]["params"]["train"]["routes"]["arrival"] = $next_train;
                        $next_train_due = $train_params["engine"][$teams[$tm]["params"]["train"]["engine"]];
                        $minutes = floor($next_train_due / 60);
                        $seconds = $next_train_due % 60;
                        $next_train_pretty = sprintf("%dm %02ds", $minutes, $seconds);
                        $comment = "Train has departed for the mine, returning in $next_train_pretty";
                    }
            } else {
                $next_train = $teams[$tm]["params"]["train"]["routes"]["arrival"] - $t;
                $minutes = floor($next_train / 60);
                $seconds = $next_train % 60;
                $next_train_pretty = sprintf("%dm %02ds", $minutes, $seconds);
                $comment = "Next train due: $next_train_pretty";
            }
        }

        //Depot
        if($cp['type'] == 'depot'){
            if($cp_option == 1){
                if($teams[$tm]["params"]["train"]["engine"]< 2) {       
                    if($teams[$tm]["params"]["score"] > 10){
                        $teams[$tm]["params"]["score"] -= 10;
                        $teams[$tm]["params"]["train"]["engine"] += 1;
                        $engine_level = $teams[$tm]["params"]["train"]["engine"] + 1;
                        $comment = "Train engine upgraded to Level $engine_level";
                    } else {
                        $comment = "You don't have enough gold for this upgrade";
                    }
                } else {
                    $comment = "The engine is already fully upgraded";
                }
            }
            
            if($cp_option == 2){
                if($teams[$tm]["params"]["train"]["carriages"]< 2) {       
                    if($teams[$tm]["params"]["score"] > 10){
                        $teams[$tm]["params"]["score"] -= 10;
                        $teams[$tm]["params"]["train"]["carriages"] += 1;
                        $engine_level = $teams[$tm]["params"]["train"]["carriages"] + 1;
                        $comment = "You now have $engine_level carriages";
                    } else {
                        $comment = "You don't have enough gold for this upgrade";
                    }
                } else {
                    $comment = "You cannot pull any more carriages";
                }
            }
            
            if($cp_option == 3){
                if($teams[$tm]["params"]["train"]["science"]< 1) {       
                    if($teams[$tm]["params"]["score"] > 30){
                        $teams[$tm]["params"]["score"] -= 30;
                        $teams[$tm]["params"]["train"]["science"] += 1;
                        $engine_level = $teams[$tm]["params"]["train"]["science"] + 1;
                        $comment = "You have hired a scientist!";
                    } else {
                        $comment = "You don't have enough gold to hire a scientist";
                    }
                } else {
                    $comment = "You already have a scientist";
                }
            }
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
                    $comment = "game started.";
                
            } 
            elseif ($teams[$tm]["params"]["game"]["game_state"] == 2) {
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
                        $teams[$tm]["score"] += $finish_bonus;
                        unset($checkpoint);
                        $comment = "Finished. Bonus: $finish_bonus";
                    if($pl == $user_ID){
                        foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                            $checkpoint["available"] = false;
                        }
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
$response["game_state"] = [$teams[$tm]["params"]["game"]["game_state"],$teams[$tm]["params"]["game"]["game_start"],$teams[$tm]["params"]["game"]["game_end"],$stage_time];
$response["inventory"] = $players[$user_ID]["inventory"];
}
$response["teams"] = $teams;
$response["live_scores"] = $final_results;
$response["commentary"] = $teams[$this_team]["params"]["commentary"];
$response["debug_log"] = $debug_log;
$response["db_response"] = $db_response;
echo json_encode($response);