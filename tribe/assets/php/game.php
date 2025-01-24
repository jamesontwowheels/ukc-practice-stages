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
$game = 5;
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
    $debug_log[] = $teams;

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
            "name" => $usernames[$row4["player_ID"]]];
    $debug_log[]  = "73. count team_members";
    }
}

$i = 0;

//build punches list
$player_cps = [];
$all_punches = [];
$players = [];

foreach ($result as $row) {
    if(!in_array($row["Player_ID"],$players)){
        $players[$row["Player_ID"]] = [];
    }
  $all_punches[] = [$row["CP_ID"],$row["Time_stamp"],$row["puzzle_answer"],$row["Player_ID"],$player_details[$row["Player_ID"]]["team"],$row["cp_option"]]; //this has all punches now.
   $i += 1;
}
$debug_log[] = $all_punches;

//build the teams

$x = 0;

// GAME SPECIFIC
//set-up the static constants (each requires it's own rule...):

    // e.g. $cps_letters = [1,2,3,4,5,6,7];
     //Bulk CPS
     $cps_holes = [1,2,3,4,5,6,7,8,9];
     $cps_monkey = [21,22,23,24,25];
     $cps_muster = [31,32,33];

     //special CPS;
     $cp_mountain = [100];
     $cp_start_finish = [998,999];
    
    $all_cps = array_merge($cps_holes,$cps_monkey,$cps_muster,$cp_mountain,$cp_start_finish);
    
    $initial_cps = array_merge($cps_holes,$cps_muster,$cp_mountain,$cp_start_finish,[21]);

    $cp_names = [
        1 => "Lion 1",
        2 => "Lion 2",
        3 => "Lion 3",
        4 => "Rhino 1",
        5 => "Rhino 2",
        6 => "Rhino 3",
        7 => "Hyena 1",
        8 => "Hyena 2",
        9 => "Hyena 3",
        21 => "Monkey 1",
        22 => "Monkey 2",
        23 => "Monkey 3",
        24 => "Monkey 4",
        25 => "Monkey 5",
        31 => "Lion base",
        32 => "Rhino base",
        33 => "Hyena base",
        100 => "View Point",
        998 => "Finish",
        999 => "Start"
        ];

    $cp_options = [
            1 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            2 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            3 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            4 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            5 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            6 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            7 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            8 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            9 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            21 => [1 => "Submit"],
            22 => [1 => "Submit"],
            23 => [1 => "Submit"],
            24 => [1 => "Submit"],
            25 => [1 => "Leave animals"],
            31 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            32 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            33 => [1 => "Pick-up Animal", 2 => "Leave animal"],
            100 => [],
            998 => [1 => "Finish"],
            999 => [1 => "Start"]
    ];
    
        // $teams = [1,2,3];

        $animal_locations = [
            1 => [
                'king' => [32, 5, 0], //[team,count,start time]
                'bush' => [32 => 0, 33 => 0, 34 => 0],
            ],
            2 => [
                'king' => [32, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            3 => [
                'king' => [32, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            4 => [
                'king' => [33, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            5 => [
                'king' => [33, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            6 => [
                'king' => [33, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            7 => [
                'king' => [34, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            8 => [
                'king' => [34, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            9 => [
                'king' => [34, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            31 => [
                'king' => [32, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            32 => [
                'king' => [33, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
            33 => [
                'king' => [34, 5, 0],
                'bush' => [32 => 0, 33 => 0, 34 => 0]
            ],
        ];
        
        $monkey_progress = [
            32 => 21,
            33 => 21,
            34 => 21
        ];

        $puzzle_cps = [21,22,23,24,25];

        $puzzle_questions = [
            21 => "1",
            22 => "2",
            23 => "3",
            24 => "4",
            25 => "5"
        ];

        $puzzle_answers =[
            21 => "1",
            22 => "2",
            23 => "3",
            24 => "4",
            25 => "5"
        ];

        $this_cp_names = $cp_names; //required if cpnames are going to change.

    //results catchers (don't change this, it's solid)
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    $available_cps = []; //which CPs are immediately available?
    $live_result = [];

    //values
    $hand_limit = 3;
    $stage_time = 75*60;
    $alert = 0;

//TEAM SPECIFIC catchers (customise the catchers here)

    //  for each team
    // monkey level
    // score
    $score = [32 => 0, 33 => 0, 34 => 0];
    $available_cps = [32 => $initial_cps, 33 => $initial_cps, 34 => $initial_cps];
    // chat?
    // team name
    

//PLAYER SPECIFIC customise $players here

    foreach($players as $player){
        $player["hand"] = 0;
        $player["history"] = [];
    }
    // for each player
    // history
    // inventory


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
    $commentary = [];
    $count_cps = count($all_punches);
    $y = 0;
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

        $cp = $all_punches[$z][0];// $cps[$z];
        $t = $all_punches[$z][1]; //times[$z];
        $puzzle_answer = strtolower($all_punches[$z][2]);
        $pl = $all_punches[$z][3];
        $tm = $all_punches[$z][4];
        $purp = $all_punches[$z][5];
        $z += 1;
        $puzzle_response = 0;
        $alert = 0;
        $game_time = $t - $game_start;


        //visit a watering hole

        if(in_array($cp, $cps_holes)){
            $ownership_check = false;
            $al = $animal_locations[$cp];
            if($al["king"][0] == $tm)
                {
                    $hole_owned = 1;
                    $current_animals = $al["king"][1];
                    $current_time = $al["king"][2];
                } else {
                    $hole_owned = 0;
                    $current_animals = $al["bush"][$tm];
                    $current_time = $al["king"][2];
                }
            if($purp == 1) //pick up an animal
            {
                if($players[$pl]["hand"] < $hand_limit)
                {
                    if($current_animals > 0)
                        {
                            $players[$pl]["hand"] += 1;
                            if($hole_owned){
                            $al["king"][1] -= 1;
                            //check for change of ownership
                            $ownership_check = true;
                            } else {
                                $al["bush"][$tm] -= 1;
                            }
                            $comment = "animal picked up";
                        } else {
                            $comment = "no animals available";
                        }
                } else {
                    $comment = "you can't pick up any more animals";
                }
            } else //deploy an animal
            {
                if($players[$pl]["hand"] > 0) {
                    $players[$pl]["hand"] -= 1;
                    if($hole_owned){
                        $al["king"][1] += 1;
                        } else {
                            $al["bush"][$tm] += 1;
                            //check for change of ownership
                            $ownership_check = true;
                        }
                        
                        $comment = "animal left";
                } else {
                    $comment = "you are not carrying any animals";
                }

            }
            if($ownership_check){
                foreach($al["bush"] as $key => $value){
                    if($value > $al["king"][1]){
                        //score the points
                        $score[$al["king"][0]] += ($t - $al["king"][2]);
                        //change the ownership
                        $al["king"][0] = $key;
                        $al["king"][1] = $value;
                        $al["king"][2] = $t;
                        $al["bush"][$key] = 0;
                        $comment = $comment.". Team $tm are now in control of the watering hole $cp";
                    }
                }
            }
            $animal_locations[$cp] = $al; // update master animal locations.
        }

            //visit a muster point
        
        //visit the mountain
        //is there even a checkpoint here??

        //visit a monkey point

        if(in_array($cp,$puzzle_cps)){
            $debug_log[] = "puzzle pinged";
            if($cp == $monkey_progress[$tm]){
                $debug_log[] = "right puzzle";
                if($puzzle_answer == $puzzle_answers[$cp]){
                    $debug_log[] = "puzzle right";
                    $monkey_progress[$tm] += 1;
                    $available_cps[$tm][] = $monkey_progress[$tm];
                    $comment = "Puzzle solved! Now go find ".$cp_names[$monkey_progress[$tm]];
                }
            } else {
                $comment = "this puzzle has been solved!";
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
$response["available_cps"]= $available_cps[$this_team]; //available_cps[$user_ID]; THIS NEEDS TO BE UPDATED!!!
//don't send back a puzzle response if nothing has been submitted.
if($incoming_cp > 0) {
$response["puzzle_response"]=$puzzle_response;
$response["alert"] = $alert;
$response["comment"] = $comment;}
$response["watering_holes"]= $cps_holes;
$response["puzzle_cps"] = $puzzle_cps;
$response["puzzle_questions"] = $puzzle_questions;
$response["running_score"] = $running_score;
$response["alert"] = $alert;
$response["commentary"] = $commentary;
$response["cp_names"] = $this_cp_names;
$response["this_team"] = $this_team;
$response["cp_options"] = $cp_options;
$response["animal_locations"] = $animal_locations;
$response["teams"] = $teams;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
$response["inventory"] = [$bags[$user_ID],$sacks[$user_ID]];
}
$response["live_scores"] = $live_result;

$response["debug_log"] = $debug_log;
echo json_encode($response);
