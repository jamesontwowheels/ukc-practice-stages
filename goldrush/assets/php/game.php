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
                "score" => 0,
                "commentary" => [],
                "level" => 0,
                "drones" => [],
                "next_horse_ready" => 0,
                "ranch_horses" => [],
                "train" => ["engine" => 1,
                            "carriages" => 2,
                            "science" => 1]
                
                //"ghost_cps" => [],
                //"snakes" => [],
                //"snake_score" => [],
                //"fruit" => 0,
                //"fruit_box" => [],
                //"location" => 0,
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
    $player_details = [];
    while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
       $teams[$row4["team"]]["members"][] = $row4["player_ID"];
       $teamed_players[] = $row4["player_ID"];
       if($row4["player_ID"] == $user_ID){
        $this_team = $row4["team"];
       }
       //set-up the player
       $player_details[$row4["player_ID"]] = [ 
            "team" => $row4["team"],
            "name" => $usernames[$row4["player_ID"]],
            "params" => [ "used_cps" => []]
        ];
    $debug_log['player details'] = $player_details;
    }
}

$i = 0;

//build punches list
$player_cps = [];
$all_punches = [];
$players = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if(!in_array($row["Player_ID"],$players)){
        $players[$row["Player_ID"]] = []; //this means that we're only initiating players once they have hit their first checkpoint, does this matter?
    }
  $all_punches[] = [$row["CP_ID"],$row["Time_stamp"],$row["puzzle_answer"],$row["Player_ID"],$player_details[$row["Player_ID"]]["team"],$row["cp_option"]]; //this has all punches now.
   $i += 1;
}
$debug_log[] = $all_punches;

//build the teams

$x = 0;

// GAME SPECIFIC
//set-up the static constants (each requires it's own rule...):

     //special CPS;
     $cp_start_finish = [998,999];

    include('cert_bible.php');

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
        $player["hand"] = 0;
        $player["history"] = [];
        $player["inventory"] = [
            "Gold" => 0,
            "Tame horses" => 0,
            "Wild horses" => 0
        ];
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
    $game_state = 0;
    $game_start = 0;
    $game_end = 0;
    $game_time = 0;
    $time_penalty = 0;
    $current_timezone = 0;


    // cycle through the punch list;
    $z = 0;
    
    while ($z < $count_cps){
 
        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp_number = intval($all_punches[$z][0]);
        $cp = $cp_bible[$cp_number];// $cps[$z];
        $t = $all_punches[$z][1]; //times[$z];
        $puzzle_answer = strtolower($all_punches[$z][2]);
        $pl = intval($all_punches[$z][3]);
        $tm = intval($all_punches[$z][4]);
        $cp_option = intval($all_punches[$z][5]);
        $purp = $all_punches[$z][5];
        $debug_log['297'] = $all_punches[$z];
        $z += 1;
        $puzzle_response = 0;
        $alert = 0;
        $game_time = $t - $game_start;
        $timezone = floor($game_time/1800);

        if($timezone != $current_timezone){
            foreach ($player_details as $player_ID => &$details) {
                $details["params"]["used_cps"] = []; // Reset "used_cps" to an empty array
            }
            unset($details);
            foreach ($teams as $team_ID => &$team) {
                $team["params"]["ghost_cps"] = []; // Reset "ghost_cps" to an empty array
            }
            unset($team);
            $current_timezone = $timezone;
        }

        //cp types: Charging Points, Drop point, Horses, Mine, Bank, Station, Depot

            /**
             * Drones,  
             * if length active_drones = 7, update puzzle level and update puzzles in bible, puzzle bible should be in team params so it can be customised to each
             * Drone route scoring (to be done at start of CP ):
             * Active routes: Final CP time - Route start (t) x SCORE_MULTIPLIER
             * Inactive routes: Route end (t) - Route start (t) x SCORE_MULTIPLIER 
             */

        //Wild Horses
        if($cp["type"] == "horse") {
            if($cp["cp"] == 13) {
                $players[$pl]["inventory"]["Wild horses"] = 0;
                $players[$pl]["inventory"]["Gold"] = 0;
                $comment = "Stand and deliver!";
                if($tm == $this_team){
                    $cp_bible[$cp_number]["message"] = "Oh no! You have tried to steal the bandit's horse and ended up losing your gold and wild horses";
                    $cp_bible[$cp_number]["options"] = [];
                    $cp_bible[$cp_number]["available"] = false;
                }
            } else {
                $players[$pl]["inventory"]["Wild horses"] += 1;
                $comment = "Horse lassoed!";
                if($tm == $this_team){
                    $cp_bible[$cp_number]["message"] = "You have successfully collected this horse";
                    $cp_bible[$cp_number]["options"] = [];
                    $cp_bible[$cp_number]["available"] = false;
                }
            }
        }

        //Ranch
        if($cp['type'] == "ranch"){
            if($cp_option == 1){
                //drop wild horses
                $whs = $players[$pl]["inventory"]["Wild horses"];
                if($whs>0){
                for ($i = 0; $i < $whs; $i++) {
                    $horse_ready = max($next_horse_ready + 120, $t + 120);
                    $next_horse_ready = $horse_ready;
                    $teams[$tm]["params"]["ranch_horses"] = [$horse_ready,0];
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
                    } elseif ($time[0] >= 1 && $time[1] == 1){
                        $horses_in_training[] = $time - $t;
                        ///THIS ISNT FINISHED!!!!
                    }
                }
                unset($time);
                $players[$pl]["inventory"]["Tame horses"] += $new_horses;
                $comment = "$new_horses trained horses collected";
            }
        }

        //Drones
        if($cp['type'] == "drone"){
            if($puzzle_answer == $cp["puzzle_a"]){
            $teams[$tm]["params"]["drones"][] = $cp;
            //check on level-up
            if(count($teams[$tm]["params"]["drones"]) == 7) {
                $teams[$tm]["params"]["level"] += 1;
                if($teams[$tm]["params"]["level"] < 4){
                    //level-up
                    
                    
                }
            }
            $comment = "puzzle solved, drone point activated";
            if($tm == $this_team){
                $cp_bible[$cp_number]["message"] = "You have successfully collected this fruit";
                $cp_bible[$cp_number]["options"] = [];
            }
        }

        //Fruit stops

        if($cp['type'] == 'fruit'){
            if(in_array($cp_number,$teams[$tm]["params"]['fruit_box'])){
                $comment = "Fruit already collected";
            } else {
                if($puzzle_answer == $cp["puzzle_a"]){
                    $teams[$tm]["params"]["fruit_box"][] = $cp_number;
                    $teams[$tm]["params"]["fruit"] += 1;
                    $comment = "puzzle solved, fruit collected";
                    if($tm == $this_team){
                        $cp_bible[$cp_number]["message"] = "You have successfully collected this fruit";
                        $cp_bible[$cp_number]["options"] = [];
                    }
                }
                else {
                    $comment = "incorrect answer";
                }
            }
        }

        //start_finish
        if($cp['type'] == "start_finish"){
            if($cp_number == 999){
            if($game_state == 0)
            {
                require 'start_game.php';

                $game_state = 1;
                $game_start = $t;
                    foreach ($cp_bible as &$checkpoint) {
                        $checkpoint["available"] = true;
                    }
                    unset($checkpoint);
                    $cp_bible[999]["available"] = false;
                    $comment = "game started.";
                
            } 
            elseif ($game_state == 2) {
               $game_state = 0;
               $game_start = 0;
               $game_end = 0;
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
                    $finish_bonus = 60/(count($teams[$tm]["members"]));
                        $teams[$tm]["score"] += $finish_bonus;
                        unset($checkpoint);
                        $comment = "Finished. Bonus: $finish_bonus";
                    if($pl == $user_ID){
                        foreach ($cp_bible as &$checkpoint) {
                            $checkpoint["available"] = false;
                        }
                        

                        $cp_bible[999]["available"] = true;
                    }
                }
            }
        }
        //
        

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
   
}
$final_results = [];

foreach ($teams as $team) {
    if (isset($team['name']) && isset($team['score'])) {
        $final_results[$team['name']] = $team['score'] + $team['params']["location"] + $team['params']["score"];
    }
}

$debug_log[] = $final_results;
$debug_log[] = $teams;

//CHOOSE WHAT TO ISSUE BACK, BASED ON PORPOISE

if($_REQUEST["purpose"] !== 2){
    //GAME SPECIFIC
    //UNIVERSAL
$response["cp_bible"]= $cp_bible; //available_cps[$user_ID]; THIS NEEDS TO BE UPDATED!!!
//don't send back a puzzle response if nothing has been submitted.
if($incoming_cp > 0) {
$response["puzzle_response"]=$puzzle_response;
$response["comment"] = $comment;}
$response["running_score"] = $running_score;
$response["alert"] = $alert;
$response["this_team"] = $this_team;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
$response["inventory"] = [
    "Position on Board" => $teams[$this_team]["params"]["location"],
    "Fruit in basket" => $teams[$this_team]["params"]["fruit"]
];
}
$response["teams"] = $teams;
$response["live_scores"] = $final_results;
$response["commentary"] = $teams[$this_team]["params"]["commentary"];
$response["debug_log"] = $debug_log;
echo json_encode($response);
