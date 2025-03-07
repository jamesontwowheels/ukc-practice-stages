<?PHP
session_start();

$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$debug = 1;
$teams_active = true;
$response = [];
$debug_log = [];
$debug_log[] = "data play";
$user_input = $_REQUEST["user_input"];
$game = 4;
$incoming_cp = $cp;
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

$usernames = [];
$query2 = "select * from dbo.users";

//don't strictly always need all users. but haven't got a viable filter on this yet.
$stmt = $conn->prepare($query2);
    $stmt->execute();
while ($row2 = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
            "cps" => []
        ];
    $debug_log[]  = "58. count teams";
    }

    //get all the team_members
    $query4 = "select * from dbo.team_members where game = :game and location = :location";
    $stmt4 = $conn->prepare($query4);
    // Bind values to the placeholders
    $stmt4->bindValue(':game', $game, PDO::PARAM_INT);
    $stmt4->bindValue(':location', $location, PDO::PARAM_INT);
    $stmt4->execute();
    $teamed_players = [];
    while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
       $teams[$row4["team"]]["members"][] = $row4["player_ID"];
       $teamed_players[] = $row4["player_ID"];
       if($row4["player_ID"] == $user_ID){
        $this_team = $row4["team"];
       }
       
    $debug_log[]  = "73. count team_members";
    }
}

$i = 0;

//build punches list
$player_cps = [];
$players = [];

foreach ($result as $row) {
    if(!in_array($row["Player_ID"],$players)){
        $players[] = $row["Player_ID"];
       // $player_cps[] = $row["Player_ID"];
        $player_cps[$row["Player_ID"]] = [];
    }
   $player_cps[$row["Player_ID"]][] = [$row["CP_ID"],$row["Time_stamp"],$row["puzzle_answer"],$row["Player_ID"]];
   $i += 1;
}
$debug_log[] = $player_cps;

//build the teams

if($teams_active == true){

    foreach ($teams as $key => $team) {
        foreach ($team["members"] as $team_member){
            if (isset($player_cps[$team_member])) {
            $team["cps"] = array_merge($team["cps"],$player_cps[$team_member]);
            }
        }
        usort($team["cps"], function ($a, $b) {
            return $a[1] <=> $b[1]; // Compare the second elements
        });
        
        $debug_log[] = "key = ".$key;
        $teams[$key]["cps"] = $team["cps"];
    }   
    
    //check for stragglers? YES WE SHOULD DO THIS!!!!!!!!!!!!!!
    $stragglers = array_diff($players, $teamed_players);
    foreach($stragglers as $straggler){
        $strag_name = "solo_".$straggler;
        if($straggler == $user_ID){
            $this_team = $strag_name;
        }
        $teams[$strag_name] = [
            "name" => $usernames[$straggler],
            "members" => [$straggler],
            "cps" => [$player_cps[$straggler]]
        ];
    }
    $count_results = count($teams);
    
    $debug_log[]  = "count teams = $count_results";
    
    $debug_log[]  = $teams;
}
else {
if($debug == 1){ $debug_log[] = '19';};
$count_results = count($player_cps);
}
$x = 0;

// GAME SPECIFIC
//set-up the static constants (each requires it's own rule...):

    // e.g. $cps_letters = [1,2,3,4,5,6,7];
     //Bulk CPS
     $cps_resources = [1,2,3,4,5,6];
     $cps_elves = [11,12,13,14,15,16];
     $cps_kids = [21,22,23,24,25,26,27];
     $cps_santas = [101,102];

     //special CPS;
     $cp_workshop = 51;
     $cp_start_finish = [998,999];
     $cp_ = 34;
    
    $all_cps = [1,2,3,4,5,6,11,12,13,14,15,16,21,22,23,24,25,26,51,101,102,998,999];
    $outside_cps = [1,2,3,4,5,6,21,22,23,24,25,26,51,102,998];
    $inside_cps = [11,12,13,14,15,16,51,101,102,998];
    
    $santa_info = [
        101 => "The children are located as follows:  CH1 = Olivia, CH2 = Noah, CH3 = Amelia, CH4 = George, CH5 = Isla, CH6 = Leo",
        102 => "Mrs Claus says thanks for selling her your stuff!"//"The Resources are located as follows: R1 = Wool, R2 = Wood, R3 = Plastic, R4 = Carbon, R5 = Metal, R6 = Lithium"
    ];

    $cp_names = [
        1 => "Wool",
        2 => "Wood",
        3 => "Plastic",
        4 => "Carbon",
        5 => "Metal",
        6 => "Lithium",
        11 => "Jumper",
        12 => "Tree House",
        13 => "Bike",
        14 => "Playstation",
        15 => "RC Car",
        16 => "Laptop",
        21 => "Ada",
        22 => "Ben",
        23 => "Cat",
        24 => "Dom",
        25 => "Isla",
        26 => "Leo",
        51 => "W/shop door",
        //101 => "Mr Claus",
        102 => "Mrs Claus",
        998 => "Finish",
        999 => "Start"
        ];
    $puzzle_cps = [];
    $this_cp_names = $cp_names; //required if cpnames are going to change.

    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    $available_cps = [999]; //which CPs are immediately available?
    $live_result = [];
    //values
    $stage_time = 45*60;
    $alert = 0;


//start looping the contestants:
foreach($teams as $team_UID => $team){

    if($_REQUEST["purpose"] != 2){
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
    $this_cp_names = $cp_names;

    $wishlists = [
        21 => [11],//[11,12,13],
        22 => [12],//[11,12,14],           //OG = [14,15,16],
        23 => [13],//[11,13,14],           //OG = [16,11,12],
        24 => [14],//[12,13,14],           //OG = [13,14,15],
        25 => [15],//[12,14,11],
        26 => [16],// [15,13,16]
    ];

    $gift_times = [
        11 => 120,
        12 => 120,
        13 => 120,
        14 => 120,
        15 => 120,//360,
        16 => 120];

    $gift_states = [0,0,0,0,0,0];

    $gift_recipes = [
        11 => [1,1,1],
        12 => [2,2,5,2],
        13 => [4,4,5,3],
        14 => [3,5,3,6],
        15 => [3,4,5,6],
        16 => [5,3,6,6]
    ];

    $gift_score = [
        11 => 10,
        12 => 10,
        13 => 10,
        14 => 10,
        15 => 10,
        16 => 10
    ];

    $gift_count = [
        11 => 0,
        12 => 0,
        13 => 0,
        14 => 0,
        15 => 0,
        16 => 0
    ];

    $gifted = [];

    $build_states = [
        11 => [0,[],0],
        12 => [0,[],0],        
        13 => [0,[],0],
        14 => [0,[],0],
        15 => [0,[],0],
        16 => [0,[],0]
    ];

    $resource_start = [
        1 => 300,
        2 => 300,
        3 => 100,
        4 => 100,
        5 => 600,
        6 => 600
    ];

    $resource_used = [
        1 => 0,
        2 => 0,
        3 => 0,
        4 => 0,
        5 => 0,
        6 => 0
    ];

    $resource_available = [];

    $resource_refresh = [
        "1" => 300,
        "2" => 300,
        "3" => 600,
        "4" => 600,
        "5" => 900,
        "6" => 900
    ];

    $resource_refresh_vol = [
        "1" => 1,
        "2" => 1,
        "3" => 4,
        "4" => 2,
        "5" => 2,
        "6" => 2
    ];

    

    foreach($team["members"] as $team_member){
        $bags[$team_member] = [];
        $sacks[$team_member] = [];
        $map_level[$team_member] = 1;
        $available_cps[$team_member] = [999];
        $team_player_count += 1;
        $debug_log[] = "bags = ";
        $debug_log[] = $bags;
    }
    
//GENERIC player specific starting values
    $id = $x;
    $results_ids[] = $id;
    $results_names[$id] = [$name,$surname];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    $commentary = [];
    $count_cps = count($team_result);
    $y = 0;
    $cps = []; //pretty sure this is defunct TO BE TESTED AND REMOVED
    $times = []; //same as above, except definitely sure it's garbage
    $running_score = 0;
    $game_state = 0;
    $game_start = 0;
    $game_end = 0;
    $game_time = 0;
    $time_penalty = 0;


    // cycle through the punch list;
    $z = 0;
    
    while ($z < $count_cps){
 
        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp = $team_result[$z][0];// $cps[$z];
        $t = $team_result[$z][1]; //times[$z];
        $puzzle_answer = strtolower($team_result[$z][2]);
        $pl = $team_result[$z][3];
        $z += 1;
        $puzzle_response = 0;
        $alert = 0;
        $game_time = $t - $game_start;


        //pick up materials
        if (in_array($cp,$cps_resources)){
            
            foreach($cps_resources as $resource){
                $gross_resource = $resource_start[$resource] + $resource_refresh_vol[$resource]*floor($game_time/$resource_refresh[$resource]);
                $net_resource = $gross_resource - $resource_used[$resource];
                $resource_available[$resource] = $net_resource;
            }

            //if resource is available
            if($resource_available[$cp] < 1){
                $early = $resource_refresh[$cp] - $game_time % $resource_refresh[$cp];
                $comment = "Resource unavailable. Wait $early seconds";
            } elseif (count($bags[$pl]) == 4) {
                $comment = "You can't carry any more, you can visit Mrs Claus to sell?";
                $resource_used[$cp] += 1;
            } else {
            //add to bag
                array_unshift($bags[$pl], $cp);                 // Add the element to the front of the array
                $name_temp = $cp_names[$cp];
                $comment = "$name_temp collected";
                // array_slice($bags[$pl], 0, 3);  //drop any extra items
                $resource_used[$cp] += 1;
                // $resource_states[$cp] = $t + $resource_refresh[$cp];            //and set time-out on next resource
            }
        } 

        //build/collect a toy
        if  (in_array($cp,$cps_elves)){
            //if in build mode
            if($build_states[$cp][0] == 0){ 
                //if right thing held
                $current_length = count($build_states[$cp][1]);
                $resource_required = $gift_recipes[$cp][$current_length];
                if(in_array($resource_required,$bags[$pl])){
                    $build_states[$cp][1][] = $resource_required;   // do the build step
                    $index = array_search($resource_required, $bags[$pl]);
                    // If the value exists, remove the first occurrence
                    if ($index !== false) {
                    array_splice($bags[$pl], $index, 1);  // Remove the thing from the bag
                    }
                    $current_length += 1;
                    $comment = "Build step $current_length on $cp_names[$cp] taken.";
                if($current_length == count($gift_recipes[$cp]) ){
                    $build_states[$cp][0] = 1;
                    $build_states[$cp][2] = $t + $gift_times[$cp]; 
                    $comment = $comment . " Build in progress!";
                }
                } else {
                    $comment = "Right resource not held";
                }
                //and if the toy has all the parts, set it to build         
            } else {
            //if in collect mode
            if($t > $build_states[$cp][2]){
              $sacks[$pl][] = $cp;
              $build_states[$cp][0] = 0;
              $build_states[$cp][1] = [];
              $comment = "Gift $cp collected";  
            }
            else {
                $comment = "Gift not ready yet";
            }
            }
        }

        //Deliver presents:

        if(in_array($cp,$cps_kids)) {
            //deliver a present
            $this_wishlist = $wishlists[$cp];
            $comment = "No presents found for ".$cp_names[$cp];
            foreach($this_wishlist as $present){
                $key = array_search($present, $sacks[$pl]);
                if ($key !== false) {
                array_splice($sacks[$pl], $key, 1);  // Remove from sack
                // unset($sacks[$pl][$key]); // remove from sack
                
                $key2 = array_search($present,$this_wishlist);
                unset($wishlists[$cp][$key2]); //remove from wishlist
                $comment = $cp_names[$present]." delivered to ".$cp_names[$cp]." +".$gift_score[$present];

                //scoring happens here:
                $running_score += $gift_score[$present];

                    //all gifts to a kid
                    if(count($wishlists[$cp]) == 0){
                        $running_score += 20;
                        $comment = $comment.". Wishlist complete, +20 bonus";
                    }

                    //any gift to all kids
                    if(!in_array($cp,$gifted)){
                        $gifted[] = $cp;
                        if(count($gifted)==6){
                            $running_score += 30;
                            $comment = $comment.". All nice kids gifted, +30 bonus";
                        }
                    }

                    //all gifts of a type
                    $gift_count[$present] += 1;
                    if($gift_count[$present] == 3){
                        $running_score += 20;
                        $comment = $comment.". All ".$cp_names[$present]." gifted, +20 bonus";
                    }

                break;
            }
        
            }
        }

        //exit the north pole
        if($cp == $cp_workshop){
            if($game_time < 1){
                $comment = "The portal is not open yet";
            } else {
                if($map_level[$pl] == 0){
                    $map_level[$pl] = 1;
                    $available_cps[$pl] = $outside_cps;
                    $comment = "Leaving the workshop";
                } else {
                    $map_level[$pl] = 0;
                    $available_cps[$pl] = $inside_cps;
                    $comment = "entering the workshop";
                }
            }
        }

        //Get info from the clauses
        if(in_array($cp,$cps_santas)){
            $alert = $santa_info[$cp];
            $comment = "$cp_names[$cp] visited";
            if(count($bags[$pl]) >0){
                $sell = count($bags[$pl]) /2;
                $bags[$pl] = [];
                $running_score += $sell;
                $comment = $comment.". Resource sold for $sell units";
            }
        }

        //start_finish
        if(in_array($cp,$cp_start_finish)){
            if($cp == 999){
            if($game_state == 0)
            {
                $game_state = 1;
                $team_finish_count = 0;
                $game_start = $t;
                $comment = "game started";
                foreach($team["members"] as $team_member){
                    $available_cps[$team_member] = $outside_cps;}
            } 
            elseif ($game_state == 2) {
               $game_state = 0;
               $game_start = 0;
               $game_end = 0;
               $comment = "game reset";
           } }
            elseif
            ($cp == 998){
                $team_finish_count += 1;
                $available_cps[$pl] = [999];
                $comment = "game partially ended";
                if($team_finish_count == $team_player_count){
                $game_state = 2;
                $game_end = $t;
                $comment = "game completely ended";
                 //check for time penalties:    
                $finish_time = $game_end - $game_start; //update
                if($finish_time > $stage_time){
                $time_penalty = 1 + floor(($finish_time-$stage_time)/20);
            } else {$time_penalty = 0;}
        }
        }
        //
        }

        //ONCE THE CP ACTION HAS BEEN TAKEN:
        $commentary[] = "Player ".$pl." - ".$comment;
        $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
    }

    //ONCE WE HAVE CYCLED THROUGH THE CPs..

    $final_score = $running_score - $time_penalty;
    $time = $game_time;
       //live results
       $live_result[$name]=$final_score;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
   
}

//CHOOSE WHAT TO ISSUE BACK, BASED ON PORPOISE

if($_REQUEST["purpose"] !== 2){
    //GAME SPECIFIC
    //UNIVERSAL
$response["all_cps"]= $all_cps;
$response["available_cps"]= $available_cps[$user_ID];
//don't send back a puzzle response if nothing has been submitted.
if($incoming_cp > 0) {
$response["puzzle_response"]=$puzzle_response;
$response["alert"] = $alert;
$response["comment"] = $comment;}
$response["puzzle_cps"] = [];
$response["running_score"] = $running_score;
$response["alert"] = $alert;
$response["commentary"] = $commentary;
$response["cp_names"] = $this_cp_names;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
$response["inventory"] = [$bags[$user_ID],$sacks[$user_ID]];
}
$response["live_scores"] = $live_result;

$response["debug_log"] = $debug_log;
echo json_encode($response);
