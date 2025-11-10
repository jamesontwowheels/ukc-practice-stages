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
            "params" => $player_params,
            "history" => [],
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

         //pick up materials
        if ($cp['type'] == "resource"){
            
                $gross_resource = $game_params["resource_start"][$cp_number] + $game_params["resource_refresh_vol"][$cp_number]*floor($game_time/$game_params["resource_refresh"][$cp_number]);
                $net_resource = $gross_resource - $teams[$tm]["params"]["team"]["resource_picked"][$cp_number];
        
            //if resource is available
            if($net_resource < 1){
                $early = $game_params["resource_refresh"][$cp_number] - $game_time % $game_params["resource_refresh"][$cp_number];
                $comment = "Resource unavailable. Wait $early seconds";
            } elseif (count($players[$pl]["inventory"]["resources"]) == 4) {
                $comment = "You can't carry any more, you can visit Mrs Claus to sell?";
            } else {
            //add to bag
                $players[$pl]["inventory"]["resouces"][] = $cp_number;
                $name_temp = $cp["name"];
                $comment = "$name_temp collected";
                $teams[$tm]["params"]["team"]["resource_picked"][$cp_number] += 1;
            }
        } 

        //build/collect a toy
        if  ($cp['type'] == "toy"){
            //if in build mode
            if($teams[$tm]["params"]["team"]["build_states"][$cp_number][0] == 0){

            }
            if($build_states[$cp][0] == 0){ 
                //if right thing held
                $current_length = count($teams[$tm]["params"]["team"]["build_states"][$cp_number][1]);
                $resource_required = $game_params["gift_recipes"][$cp_number][$current_length];
                if(in_array($resource_required,$players[$pl]["inventory"]["resources"])){
                    $build_states[$cp][1][] = $resource_required;   // do the build step
                    $index = array_search($resource_required, $players[$pl]["inventory"]["resources"]);
                    // If the value exists, remove the first occurrence
                    if ($index !== false) {
                    array_splice($players[$pl]["inventory"]["resources"], $index, 1);  // Remove the thing from the bag
                    }
                    $current_length += 1;
                    $comment = "Build step $current_length on ".$cp['name']." taken.";
                if($current_length == count($game_params["gift_recipes"][$cp_number]) ){
                    $teams[$tm]["params"]["team"]["build_states"][$cp_number][0] = 1;
                    $teams[$tm]["params"]["team"]["build_states"][$cp_number][2] = $t + $game_params["gift_times"][$cp_number]; 
                    $comment = $comment . " Build in progress!";
                }
                } else {
                    $comment = $cp_bible[$resource_required]["name"]." required next, not held";
                }
                //and if the toy has all the parts, set it to build         
            } else {
            //if in collect mode
            if($t > $teams[$tm]["params"]["team"]["build_states"][$cp_number][2]){
              $player[$pl]["inventory"]["presents"][] = $cp_number;
              $teams[$tm]["params"]["team"]["build_states"][$cp_number][0] = 0;
              $teams[$tm]["params"]["team"]["build_states"][$cp_number][1] = [];
              $comment = "Gift ".$cp["name"]." collected";  
            }
            else {
                $comment = "Gift not ready yet";
            }
            }
        }

        //Deliver presents:

        if($cp['type'] == "toy") { /////////I've made it this far and i'm tired now... going to bed.
            //deliver a present
            $this_wishlist = $teams[$tm]["params"]["team"]["wishlists"][$cp_number];
            $comment = "No presents found for ".$cp["name"];
            foreach($this_wishlist as $present){
                $key = array_search($present,  $player[$pl]["inventory"]["presents"]);
                if ($key !== false) {
                array_splice($player[$pl]["inventory"]["presents"], $key, 1);  // Remove from sack
                
                $key2 = array_search($present,$teams[$tm]["params"]["team"]["wishlists"][$cp_number]);
                unset($teams[$tm]["params"]["team"]["wishlists"][$cp_number][$key2]); //remove from wishlist
                $present_name = $teams[$tm]["params"]["cp_bible"][$present]["name"];
                $recipient_name = $teams[$tm]["params"]["cp_bible"][$cp_number]["name"];
                $gift_score = $game_params["gift_score"][$cp_number];

                $comment = $present_name." delivered to ".$recipient_name." +".$gift_score." points";

                //scoring happens here:
                $running_score += $gift_score;

                    //all gifts to a kid
                    if(count($teams[$tm]["params"]["team"]["wishlists"][$cp_number]) == 0){
                        $running_score += 20;
                        $comment = $comment."<br>Wishlist complete, +20 bonus";
                    }

                    //any gift to all kids
                    if(!in_array($cp_number,$teams[$tm]["params"]["team"]["gifted"])){
                        $teams[$tm]["params"]["team"]["gifted"][] = $cp;
                        if(count($teams[$tm]["params"]["team"]["gifted"])==6){
                            $running_score += 30;
                            $comment = $comment."<br>All kids gifted, +30 bonus";
                        }
                    }

                    //all gifts of a type
                    $teams[$tm]["params"]["team"]["gift_count"][$present] += 1;
                    if($teams[$tm]["params"]["team"]["gift_count"][$present] == 3){
                        $running_score += 20;
                        $comment = $comment."<br> All ".$present_name." gifted, +20 bonus";
                    }

                break;
            }
        
            }
        }

        //exit the north pole
        if($cp["type"] == "portal"){
            if($pl == $user_ID){
                $player[$pl]["params"][$map_level] = 1 - $player[$pl]["params"][$map_level];
                foreach($teams[$tm]["params"]["cp_bible"] as &$checkpoint)
                {
                    if(in_array($checkpoint["cp"],$game_params["level_cps"][$player[$pl]["params"][$map_level]])){
                        $checkpoint["available"] = true;
                    } else {$checkpoint["avaialble"] = false; }
                }
                if ($player[$pl]["params"][$map_level] = 0)
                { $comment = "Leaving the workshop"; } else {
                    $comment = "Entering the workshop";}
                }
            }

        //Get info from the clauses
        if($cp["type"] == "Mrs Claus"){
            if(count($players[$pl]["inventory"]["resources"]) > 0 ){
                $sell = count($players[$pl]["inventory"]["resources"]) /2;
                $players[$pl]["inventory"]["resources"] = [];
                $running_score += $sell;
                $comment = $comment.". Resource sold for $sell units";
            }
        }

        //start_finish
        if($cp['type'] == "start_finish"){
            if($cp_number == 999){
            if($teams[$tm]["params"]["game"]["game_state"] == 0)
            {
                $teams[$tm]["params"]["game"]["game_state"] = 1;
                $teams[$tm]["params"]["game"]["game_start"] = $t;
                $debug_log[]  = "game state =" . $teams[$tm]["params"]["game"]["game_state"];
                    foreach ($teams[$tm]["params"]["cp_bible"] as &$checkpoint) {
                        if(in_array($checkpoint["cp"],$starting_cps)){
                            $checkpoint["available"] = true;} 
                            else {
                                $checkpoint["available"] = false;
                            }
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
                    $comment = "too for finish bonus";
                } else {
                    $teams[$tm]["params"]["finishers"] += 1;
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

                    if($teams[$tm]["members"] == $teams[$tm]["params"]["finishers"]){
                        //TECH DEBT: Repeatable logic for finishing
                        //TECH DEBT: Does this work for individual runners (i.e. not in a team...)
                        $remaining_mins = floor(($stage_time - $game_time)/60);
                        $finish_bonus = $remaining_mins;
                        $teams[$tm]["params"]["score"] += $finish_bonus;
                        $comment = "Finished. Bonus: $finish_bonus";
                        $teams[$tm]["params"]["cp_bible"][998]["message"] = "Your whole team have finished, and earned $finish_bonus gold";
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
}
$response["game_state"] = [$teams[$this_team]["params"]["game"]["game_state"],$teams[$this_team]["params"]["game"]["game_start"],$teams[$this_team]["params"]["game"]["game_end"],$stage_time];
$response["inventory"] = $players[$user_ID]["inventory"];
$response["teams"] = $teams;
$response["live_scores"] = $final_results;
$response["commentary"] = $teams[$this_team]["params"]["commentary"];
$response["debug_log"] = $debug_log;
$response["db_response"] = $db_response;
echo json_encode($response);