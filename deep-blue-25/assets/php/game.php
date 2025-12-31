<?PHP
session_start();

$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$game = $_SESSION['game'];
$debug = 1;
$teams_active = true;
$response = [];
$debug_log = [];
$debug_log["session data"] = [$user_ID, $location, $game];
$user_input = $_REQUEST["user_input"];
$incoming_cp = $cp;

//game specifics (to include in teams)

include 'puzzle_bible.php';
include 'cp_bible.php';
include 'params_bible.php';
include 'db_connect.php';


ini_set("allow_url_fopen", 1); //this is important for fetching remote files

//test_game to be made into a variable
$query = "select * from dbo.test_game where game = :game and location = :location";
    $stmt = $conn->prepare($query);
    // Bind values to the placeholders
    $stmt->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt->execute();

$usernames = [];
$query2 = "select * from dbo.users";
//TECH DEBT - don't strictly always need all users. but haven't got a viable filter on this yet. This will come from the pre-game page

$stmt2 = $conn->prepare($query2);
    $stmt2->execute();
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $usernames[$row2['id']] = $row2['name'];
}

if($teams_active){
    $debug_log["active teams?"]  = "44. teams active";
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
            //"score" => 0, //not in use
            "params" => [
                "finishers" => 0,
                "game" => [
                    "game_start" => 0,
                    "game_state" => 0,
                    "game_end" => 0
                ],
                "score" => 0,
                "commentary" => [],
                "level" => 0,
                "cp_bible" => $cp_bible,
                "team" => $team_params,
            ]
        ];
    }
    $debug_log["og_cp_bible"] = $cp_bible;
    //get all the team_members
    $query4 = "select * from dbo.team_members where game = :game and location = :location";
    $stmt4 = $conn->prepare($query4);
    // Bind values to the placeholders
    $stmt4->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt4->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt4->execute();

    $teamed_players = [];
    $players = [];
    $debug_log["players"] = 0;
    while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
       $debug_log["players"] += 1;
       $teams[$row4["team"]]["members"][] = $row4["player_ID"];
       $teamed_players[] = $row4["player_ID"];
       if($row4["player_ID"] == $user_ID){
        $this_team = $row4["team"];
        $debug_log["this player"] = [
            "team" => $this_team,
            "player" => $user_ID
        ];
       }
       //set-up the player
       $players[$row4["player_ID"]] = [ 
            "team" => $row4["team"],
            "name" => $usernames[$row4["player_ID"]],
            "params" => $player_params,
            "history" => [],
            "private_inventory" => $private_inventory,
            "inventory" => $player_inventory
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
$debug_log["all-punches"] = $all_punches;

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
    $stage_time = $game_params["stage_time"];
    $alert = 0;

//TEAM SPECIFIC catchers (customise the catchers here)
//TECH DEBT: this is where I will add in the 'test-user' super role

    if($user_ID == 29){
        $cp_bible[999]["available"] = true;
    }

    $pl_finishers=  [];

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
    foreach($team["members"] as $team_member){
        $team_player_count += 1;
    }
}
    
//GENERIC player specific starting values
    $id = $x;
    $results_ids[] = $id;
    $results_names[$id] = [$name,$surname];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    $count_cps = count($all_punches);
    $y = 0;
    $running_score = 0;
    $game_time = 0;
    $time_penalty = 0;
    $current_timezone = 0;


    // cycle through the punch list;
    $z = 0;
    
    while ($z < $count_cps){
 
        //INFO: add to detailed results = $results_detailed[$id][] = [_your code_];
        //INFO: add to summary results = $results_summary[$id][] = [_your code_];

        $cp_number = intval($all_punches[$z][0]);
        $pl = intval($all_punches[$z][3]);
        $tm = intval($all_punches[$z][4]);
        $cp_option = intval($all_punches[$z][5]);
        $cp =& $teams[$tm]["params"]["cp_bible"][$cp_number];// $cps[$z];
        //xtp-xpp
        $xtp =& $teams[$tm]["params"]; //this teams params
        $xpp =& $players[$pl]["params"]; //this player's params
        $debug_log["cp-name"] = $cp['name'];
        $cp_name = $cp["name"];
        $t = $all_punches[$z][1]; //times[$z];
        $puzzle_answer = strtolower($all_punches[$z][2]);
        $purp = $all_punches[$z][5];
        $debug_log['297'] = $all_punches[$z];
        $z += 1;
        $puzzle_response = 0;
        $alert = 0;
        $game_time = $t - $xtp["game"]["game_start"];
        $animation = isset($teams[$tm]["params"]["cp_bible"][$cp_number]["animation"]) ? $teams[$tm]["params"]["cp_bible"][$cp_number]["animation"] : [false,""];
        $player_data =& $players[$pl];
        $fishing_level = $xtp["team"]["fish_level"];
        
        if($game_time > $stage_time && $cp_number != 999 ){
            foreach ($cp_bible as $key => $cp) {
                    $teams[$tm]["params"]["cp_bible"][$key]['available'] = false;
                    }
                    $comment = "Time is up! The game has ended.";
                    $teams[$tm]["params"]["game"]["game_end"] = $t;
                    $teams[$tm]["params"]["game"]["game_state"] = 2;
                    //TECH DEBT: should there be some penalty logic here??

                }
                else {
        
        //ladder
        /**
         * if($cp['type'] == 'XXX'){
         * Add logic
         * $comment = "comment"
         * $teams[$tm]["params"]["score"] += SCORE;
         * }
         * 
         *
         */
        if($cp['type'] == 'XXX'){
            //Description
            $comment = "Comment";
            $teams[$tm]["params"]["score"] += 0;
            if($puzzle_answer == $cp["puzzle_a"]){};
        }

                //add oxygen
if ($cp['type'] == "hole"){
    if($player_data["params"]["oxygen"]["active"] == 0){
    $player_data["params"]["oxygen"]["active"] = 1;
    $oxygen = $t + $game_params["oxygen_time"];
    
    $player_data["params"]["oxygen"]["end"] = $oxygen;
    $comment = "Dive started";
    if($pl == $user_ID){
                    foreach($airholes as $airhole){
                    $xtp["cp_bible"][$airhole]["options"] = [1 => "Surface"];
                    };
                    $oxygen_state = [1,$oxygen];
                    // all false
                    foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                    $checkpoint["available"] = false;
                    };
                    // some true
                    $available_below_cps = array_diff($below_cps, $xtp["team"]["fish_caught"]);
                    foreach ($available_below_cps as $bcp){
                        $teams[$tm]["params"]["cp_bible"][$bcp]["available"] = true;
                    }
                } 
    } else {
        if($t > $player_data["params"]["oxygen"]["end"]){
            //drop fish
            
            $debug_log["xtp $t"] =  $xtp["team"]["fish_caught"];
            $debug_log["xpp $t"] =  $xpp["fish_held"];
            $xtp["team"]["fish_caught"] = array_diff($xtp["team"]["fish_caught"], $xpp["fish_held"]);
            $debug_log["xtp_diff $t"] = $xtp["team"]["fish_caught"];
            $player_data["inventory"]["Fish (kg)"] = 0;
            $comment = "Dive finished, but out of oxygen, all held fish released.";
             if($xpp["oxygen"]["active"] == 1){
                $water = true;
                } else { $water = false;}
                foreach($xpp["fish_held"] as $fishy){
                     $xtp["cp_bible"][$fishy]["available"] = $water;
                     $xtp["cp_bible"][$fishy]["options"] = [1 => "collect"];
                };
            $xpp["fish_held"] = [];
        } else {
        $comment = "Dive finished";
        }
        $player_data["params"]["oxygen"]["active"] = 0;
        $player_data["params"]["oxygen"]["end"] = 0;
            if($pl == $user_ID){
                    foreach($airholes as $airhole){
                    $xtp["cp_bible"][$airhole]["options"] = [1 => "Dive"];
                    }
                    $oxygen_state = [0,0];
                    // all false
                    foreach ($xtp["cp_bible"] as &$checkpoint) {
                    $checkpoint["available"] = false;
                    };
                    // some true
                    foreach ($above_cps as $acp){
                        $xtp["cp_bible"][$acp]["available"] = true;
                    }
                } 
    }
} 


//collect fish
if ($cp['type'] == "fish"){
    if ($t > $player_data["params"]["oxygen"]["end"]){
        //drop fish
        $xtp["team"]["fish_caught"] = array_diff($xtp["team"]["fish_caught"], $xpp["fish_held"]);
        $player_data["inventory"]["Fish (kg)"] = 0;
        $comment = "Out of oxygen, all held fish released.";
        if($xpp["oxygen"]["active"] == 1){
            $water = true;
        } else { $water = false;}
        foreach($xpp["fish_held"] as $fishy){
            $xtp["cp_bible"][$fishy]["available"] = $water;
            $xtp["cp_bible"][$fishy]["options"] = [1 => "collect"];
        };
        $xpp["fish_held"] = [];
        
    } else {
        if(!in_array($cp_number,$xtp['team']['fish_caught'])){
        $fish_name = $cp_name;
        $fish_three = substr($fish_name, 0, 3);
        $xtp["team"]["fish_caught"][] = $cp_number;
        $player_data["params"]["fish_held"][] = $cp_number;
        $debug_log["fish $t"] = $xpp["fish_held"];
        $fish_weight = $fish_weights[$fishing_level][$fish_three];
        $player_data["inventory"]["Fish (kg)"] += $fish_weight;
        $comment = "$fish_name caught! ".$fish_weight."kg landed";
        $xtp["cp_bible"][$cp_number]["available"] = false;
        $xtp["cp_bible"][$cp_number]["options"] = [];
        } else {
            $comment = "Fish already caught at this level";
        }
    }            
}

//Recruit Seals:
if ($cp['type'] == "seal"){
    //check recruitment
    if ($xtp["team"]["seal_state"][$cp_number]["active"] == 1){
        // get the seal haul and reset it
        $seal_haul = floor(($t - $teams[$tm]["params"]["seal_state"][$cp_number]["started"])/60)/2 - $teams[$tm]["params"]["seal_state"][$cp]["collected"];
        $teams[$tm]["params"]["seal_state"][$cp_number]["collected"] += $seal_haul;
        $player_data["inventory"]["Fish (kg)"] += $seal_haul;
        $comment = "$seal_haul kg fish collected from Seal $cp_number";
    } else {
        if($puzzle_answer == $puzzle_answers[$cp]){
            //recruit the seal
            $comment = "Puzzle solved. Seal $cp_number recruited";
            $teams[$tm]["params"]["seal_state"][$cp_number]["active"] = 1;
            $teams[$tm]["params"]["seal_state"][$cp_number]["started"] = $t;
            //update the option
            $cp["options"] = [1 => "Collect"];
            $cp["puzzle"] = false;
        } else {
            $comment = "puzzle incorrect. -2kg fee";
            $xtp["score"] -= 2;
        }
    }            
}

//take a lesson
if ($cp['type'] == "walrus"){
    if($xtp["team"]["fish_level"]<2){
    if($xtp["score"] >= $lesson_cost[$xtp["team"]["fish_level"]]){
    $xtp["score"] -= $lesson_cost[$xtp["team"]["fish_level"]];
    $comment = "Lesson $fishing_level taken! Cost of $lesson_cost[$fishing_level]";
    $xtp["team"]["fish_level"] += 1;
    foreach($below_cps as $bcp){
        $xtp["cp_bible"][$bcp]["available"] = true;
    }
    } else {
        $comment = "You don't have enough fish to pay the wise walrus, no lesson taken";
    }} else {
        $comment = "You are a fully trained Level 3 ninja fishing bear";
    }
}

//snow bank
if ($cp['type'] == "bank"){
    $xtp["score"] += $player_data["inventory"]["Fish (kg)"];
    $comment = $player_data["inventory"]["Fish (kg)"]."kg of fish banked.";
    $player_data["inventory"]["Fish (kg)"] = 0;
    $xpp["fish_held"] = [];
} 

        //start_finish
        if($cp['type'] == "start_finish"){
            if($cp_number == 999){
            if($teams[$tm]["params"]["game"]["game_state"] == 0)
            {
                $teams[$tm]["params"]["game"]["game_state"] = 1;
                $teams[$tm]["params"]["game"]["game_start"] = $t;
                $debug_log[]  = "game state =" . $teams[$tm]["params"]["game"]["game_state"];
                $debug_log["start bible"] = $teams[$tm]["params"]["cp_bible"];
                    foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                        if(in_array($checkpoint["cp"],$above_cps)){
                      //    if($players[$pl]["params"]["map_level"] == 0){
                        $checkpoint["available"] = true;
                    } else {$checkpoint["available"] = false; }
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
                    $comment = "too late for finish bonus";
                } else {
                    $teams[$tm]["params"]["finishers"] += 1;
                    
                    $finish_bonus = 15;
                    $teams[$tm]["params"]["score"] += $finish_bonus;
                    $comment = "Finished. Bonus: $finish_bonus";
                    $pl_finishers[] = $pl;
                    if($pl == $user_ID){
                        foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                            $checkpoint["available"] = false;
                        } 
                        unset($checkpoint);
                        $teams[$tm]["params"]["cp_bible"][998]["available"] = true;
                        $teams[$tm]["params"]["cp_bible"][998]["options"] = [];
                        $teams[$tm]["params"]["cp_bible"][998]["message"] = "You have finished";
                    }

                    if(count($teams[$tm]["members"]) == $teams[$tm]["params"]["finishers"]){
                        //TECH DEBT: Repeatable logic for finishing
                        //TECH DEBT: Does this work for individual runners (i.e. not in a team...)
                        $remaining_mins = floor(($stage_time - $game_time)/60);
                        $teams[$tm]["params"]["cp_bible"][998]["message"] = "Your whole team have finished, and each earned $finish_bonus gold";
                        $teams[$tm]["params"]["game"]["game_end"] = $t;
                        $teams[$tm]["params"]["game"]["game_state"] = 2;
                    }
                }
            }
        }
        //
    }

        //ONCE THE CP ACTION HAS BEEN TAKEN:
        $teams[$tm]["params"]["commentary"][] = "Player ".$pl." - ".$comment;
        $results_detailed[$id][] = [$t,$cp_number,$comment,"",$running_score,$teams[$tm]["name"],$game_time,$players[$pl]["name"]];
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
$response["comment"] = $comment;
$response["animation"] = $animation;}
$response["running_score"] = $teams[$tm]["score"];
$response["alert"] = $alert;
$response["this_team"] = $this_team;
$response["usernames"] = $usernames;
$response["oxygen_state"] = $oxygen_state;
}
$response["game_state"] = [$teams[$this_team]["params"]["game"]["game_state"],$teams[$this_team]["params"]["game"]["game_start"],$teams[$this_team]["params"]["game"]["game_end"],$stage_time];
$response["inventory"] = $players[$user_ID]["inventory"];
$response["teams"] = $teams;
$response["live_scores"] = $final_results;
$response["commentary"] = $teams[$this_team]["params"]["commentary"];
$response["debug_log"] = $debug_log;
$response["detailed_results"] = $results_detailed;
echo json_encode($response);