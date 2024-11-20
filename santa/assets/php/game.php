<?PHP
session_start();

$user_ID = $_SESSION['user_ID'];
$location = $_SESSION['location'];
$debug = 1;
$teams_active = true;
$response = [];
$debug_log = [];
$commentary = [];
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
$query = "select * from dbo.test_game where Player_ID = $user_ID AND location = $location AND game = $game ORDER BY Time_stamp ASC";

if($_REQUEST["purpose"] == 2){
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
            $team["cps"] = array_merge($team["cps"],$player_cps[$team_member]);
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
        $strag_name = "strag_".$straggler;
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
/*
    // e.g. $cps_letters = [1,2,3,4,5,6,7];
     //Bulk CPS
     $cps_resources = [1,2,3,4,5,6];
     $cps_elves = [11,12,13,14,15,16];
     $cps_kids = [21,22,23,24,25,26,27];

     //special CPS;
     $cp_workshop = 51;
     $cp_start_finish = [998,999];
     $cp_ = 34;
    
    $all_cps = [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,31,32,33,34,102,202,302,333,777,998,999];
    $outside_cps = [1,2,3,4,5,21,22,23,24,25,51,998];
    $inside_cps = [11,12,13,14,15,51,998];
    
    $cp_names = [
        1 => "Wool",
        2 => "Wood",
        3 => "Plastic",
        4 => "Carbon",
        5 => "Metal",
        6 => "Lithium",
        11 => "Elf 1",
        12 => "Elf 2",
        13 => "Elf 3",
        14 => "Elf 4",
        15 => "Elf 5",
        16 => "Elf 6",
        21 => "Olivia",
        22 => "Noah",
        23 => "Amelia",
        24 => "George",
        25 => "Isla",
        26 => "Leo",
        51 => "Portal",
        101 => "Mr Claus",
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
    $time_penalty = 0;
    //values
    $stage_time = 75*60;
//start looping the contestants:
foreach($teams as $team_UID => $team){
        //while($x < $count_results){
    /// not needed $team_UID = key($team);
    $name = $team["name"];
    $team_result = $team["cps"]; //$results[$x];
  // don't have this data yet...
    $surname = "data"; //update
    $finish_time = 0 ; //update - why is this here???

    $x += 1;

    
if($debug == 1){ $debug_log[] = '72';};
//GAME SPECIFIC set-up course/result variables for each contestants
    $this_cp_names = $cp_names;
    $gift_times = [
        11 => 0,
        12 => 0,
        13 => 0,
        14 => 0,
        15 => 0,
        16 => 0];
    $gift_states = [0,0,0,0,0,0];
    $gift_recipes = [
        11 => [1,2,3],
        12 => [1,1,1],
        13 => [2,3,4,5,2],
        14 => [1],
        15 => [5,4,5,4,5],
        16 => [5,4,5,4,5]
    ];

    $build_states = [
        11 => [0,[],0],
        12 => [0,[],0],        
        13 => [0,[],0],
        14 => [0,[],0],
        15 => [0,[],0],
        16 => [0,[],0]
    ];

    $resource_start = [
        1 => 0,
        2 => 0,
        3 => 0,
        4 => 0,
        5 => 0,
        6 => 0
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
        "1" => 0,
        "2" => 10,
        "3" => 20,
        "4" => 30,
        "5" => 40,
        "6" => 40
    ];

    $map_level = 0;

    foreach($team["members"] as $team_member){
        $bags[$team_member] = [];
        $sacks[$team_member] = [];
        $debug_log[] = "bags = ";
        $debug_log[] = $bags;
    }
    
//GENERIC
    $id = $x;
    $results_ids[] = $id;
    $results_names[$id] = [$name,$surname];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    $count_cps = count($team_result);
    $y = 0;
    $cps = []; //pretty sure this is defunct TO BE TESTED AND REMOVED
    $times = []; //same as above, except definitely sure it's garbage
    $running_score = 0;
    $game_state = 0;
    $game_start = 0;
    $game_end = 0;


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
        $game_time = $t - $game_start;


        //pick up materials
        if (in_array($cp,$cps_resources)){
            
            foreach($cps_resources as $resource){
                $gross_resource = $resource_start + floor($game_time/$resource_refresh[$resource]);
                $net_resource = $gross_resource - $resource_used[$resource];
                $resource_available[$resource] = $net_resource;
            }

            //if resource is available
            if($resource_available[$cp] < 1){
                $early = $game_time % $resource_refresh[$cp];
                $comment = "Resource unavailable. Wait $early seconds";
            } elseif (count($bags[$pl]) == 10) {
                $comment = "You can't carry any more, resource lost";
                $resource_used[$cp] += 1;
            } {
            //add to bag
                array_unshift($bags[$pl], $cp);                 // Add the element to the front of the array
                $comment = "Resource collected";
                // array_slice($bags[$pl], 0, 3);  //drop any extra items
                $resource_used[$cp] += 1;
                $resource_states[$cp] = $t + $resource_refresh[$cp];            //and set time-out on next resource
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
                    $comment = "Build step $current_length on $cp taken.";
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
            if($t < $build_states[$cp][2]){
              $sacks[$pl][] = $cp;
              $build_states[$cp][0] = 0;
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
            $index = array_search($resource_required, $bags[$pl]);
            // If the value exists, remove the first occurrence
            if ($index !== false) {
                array_splice($bags[$pl], $index, 1);  // Remove the thing from the bag
                }
        }

        //enter the north pole
        if($cp == $cp_workshop){
            if($game_time < 600){
                $comment = "The portal is not open yet";
            } else {
                if($map_level == 0){
                    $map_level = 1;
                    $available_cps = $outside_cps;
                } else {
                    $map_level = 0;
                    $available_cps = $inside_cps;
                }
            }
        }

        //start_finish
        if(in_array($cp,$cp_start_finish)){
            if($game_state == 0)
            {
                $game_state = 1;
                $game_start = $t;
                $comment = "game started";
                $available_cps = $above_cps;
            } elseif($game_state == 1){
                $available_cps = [999];
                $game_state = 2;
                $game_end = $t;
                $comment = "game ended";
                 //check for time penalties:    
                $finish_time = $game_end - $game_start; //update
                if($finish_time > $stage_time){
                $time_penalty = floor(($finish_time-$stage_time)/5);
            } else {$time_penalty = 0;}
        }
        //
             else {
                $game_state = 0;
                $game_start = 0;
                $game_end = 0;
                $comment = "game reset";
                $commentary[] = $comment;
            }
        }

        //ONCE THE CP ACTION HAS BEEN TAKEN:
        $commentary[] = $comment;
        $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
    }

    //ONCE WE HAVE CYCLED THROUGH THE CPs..

    $final_score = $running_score - $time_penalty;
       //live results
       $live_result[$name]=$final_score;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
}

//CHOOSE WHAT TO ISSUE BACK, BASED ON PORPOISE
/*
if($_REQUEST["purpose"] !== 2){
    //GAME SPECIFIC
    //UNIVERSAL
$response["all_cps"]= $all_cps;
$response["available_cps"]= $available_cps;
//don't send back a puzzle response if nothing has been submitted.
if($incoming_cp > 0) {
$response["puzzle_response"]=$puzzle_response;}
$response["running_score"] = $running_score;
$response["commentary"] = $commentary;
$response["debug_log"] = $debug_log;
$response["cp_names"] = $this_cp_names;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
}
$response["live_scores"] = $live_result;
echo json_encode($response);
*/