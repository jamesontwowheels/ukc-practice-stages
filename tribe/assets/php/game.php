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
            25 => [1 => "Claim Prize"],
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

        $muster_destination = [
            32 => 31,
            33 => 32,
            34 => 33
        ];

        $puzzle_cps = [21,22,23,24,25];

        $puzzle_questions = [
            21 => "Fill in the gaps to complete seven 5-letter words reading downwards. The inserted letters will spell out the name of a famous author.<br>
                    <table class='viewpoint'>
                    <tr><td>A</td><td>C</td><td>L</td><td>B</td><td>S</td><td>M</td><td>M</td></tr>
                    <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                    <tr><td>I</td><td>A</td><td>R</td><td>A</td><td>E</td><td>A</td><td>A</td></tr>
                    <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                    <tr><td>S</td><td>R</td><td>H</td><td>E</td><td>P</td><td>T</td><td>A</td></tr>
                    </table>
                    ",
            22 => "Which African country has been encrypted using a shift cypher? <br> <h3>MGPAC</h3>",
            23 => "The numbers in and around each square are all linked. What number should be placed in the final square?<br><br><img class='puzzle_pic' src='assets/img/missing_number_puzzle.png'>",
            24 => "How many triangles are in this picture?<br><br><img class='puzzle_pic' src='assets/img/puzzle_pic_2.png'>",
            25 => "Congratulations on reaching the Monkey Pool. Claim your prize!"
        ];

        $puzzle_answers =[
            21 => "charles dickens",
            22 => "kenya",
            23 => "12",
            24 => "24",
            25 => "5"
        ];

        $monkey_prizes = [
            32 => [0,0],
            33 => [0,0],
            34 => [0,0]
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
    $available_cps = [32 => [], 33 => [], 34 => []];
    if($user_ID == 29){
        $available_cps = [32 => $initial_cps, 33 => $initial_cps, 34 => $initial_cps];
    }

    $commentary = [32 => [], 33 => [], 34 => []];
    $live_result = [32 => 0, 33 => 0, 34 => 0];
    $pl_finishers=  [];
    

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
    //$commentary = [];
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

        if ($game_state == 1 && ($t - $game_start) > $stage_time){
            $comment = "Game Over";
            $commentary[$tm][] = "Player ".$pl." - ".$comment;
            $game_state = 2;
            foreach($cps_holes as $hole){
                $watering_hole = $animal_locations[$hole];
                $owner = $watering_hole["king"][0];
                $live_result[$owner] += 1 + ($stage_time - $watering_hole["king"][2]);
                $debug_log[] = $owner;
            }
            continue;
        }

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
                if($hole_owned == 1 && $current_animals == 1){
                    $comment = "you can't remove the last defender";
                } else {
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
                        $live_result[$al["king"][0]] += (($t - $game_start) - $al["king"][2]);
                        //change the ownership
                        $animal_locations[$muster_destination[$al["king"][0]]]["king"][1] += $al["king"][1];
                        $al["king"][0] = $key;
                        $al["king"][1] = $value;
                        $al["king"][2] = $t-$game_start;
                        $al["bush"][$key] = 0;
                        $comment = $comment.". Team $tm are now in control of the watering hole $cp";
                    }
                }
            }
            $animal_locations[$cp] = $al; // update master animal locations.
        }

            //visit a muster point

            if(in_array($cp, $cps_muster)){
                $al = $animal_locations[$cp];
                //if it's the right point
                if($al["king"][0] == $tm){
                //to pick up animals
                    if($purp == 1){
                        //if there's animals
                        if($al["king"][1] > 0){
                            //and the hand isn't full
                            if($players[$pl]["hand"] < $hand_limit){
                                $players[$pl]["hand"] += 1;
                                $al["king"][1] -= 1;
                                $comment = "animal picked-up";
                            }
                            else {
                                $comment = "your hand is full";
                            }
                        } else {
                            $comment = "there are no animals to pick-up";
                        }
                    } elseif ($purp == 2) {
                        //if there's animals to drop
                        if($players[$pl]["hand"] > 0){
                                $players[$pl]["hand"] -= 1;
                                $al["king"][1] += 1;
                                $comment = "Animal left at muster point";
                            } else {
                            $comment = "you are not carrying any animals";
                        }
                    }
                } else {
                    $comment = "this isn't your muster point";
                }
                $animal_locations[$cp] = $al; // update master animal locations.
            }
        
        //visit the mountain
        //is there even a checkpoint here?? nope

        //visit a monkey point

        if(in_array($cp,$puzzle_cps)){
            if($cp != 25){
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
            } }
            else {
                if($monkey_prizes[$tm][0] == 0){
                    $comment = "Prize already collected";
                } else {
                    $prize = $stage_time - $t;
                    $live_result[$tm] += $prize;
                    $comment = "Monkey prize of $prize collected";
                    $monkey_prizes[$tm][0] = 1;
                    $monkey_prizes[$tm][1] = $prize;
                }
            }
        }

        //start_finish
        if(in_array($cp,$cp_start_finish)){
            if($cp == 999){
            if($game_state == 0)
            {
                $game_state = 1;
                $game_start = $t;
                $comment = "game started";
                $available_cps = [32 => $initial_cps, 33 => $initial_cps, 34 => $initial_cps];
            } 
            elseif ($game_state == 2) {
               $game_state = 0;
               $game_start = 0;
               $game_end = 0;
               $comment = "game reset";
           } }
            elseif
            ($cp == 998){
                if(in_array($pl,$pl_finishers)){
                    $comment = "already finished";
                } else {
                    $pl_finishers[] = $pl;
                    $comment = "finished";
                    $live_result[$tm] += 300;
                }
            }
        }
        //
        

        //ONCE THE CP ACTION HAS BEEN TAKEN:
        $commentary[$tm][] = "Player ".$pl." - ".$comment;
        $results_detailed[$id][] = [$t,$cp,$comment,"",$running_score];
    }

    //ONCE WE HAVE CYCLED THROUGH THE CPs..

    $final_score = $running_score - $time_penalty;
    $time = $game_time;
       //live results
        //$live_result[$name]=$final_score;
        //$results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
   
}

$final_results = ["Lions" => 0, "Rhinos" => 0, "Hyenas" => 0];
$final_results["Lions"] = $live_result[32];
$final_results["Rhinos"] = $live_result[33];
$final_results["Hyenas"] = $live_result[34];


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
$response["commentary"] = $commentary[$this_team];
$response["cp_names"] = $this_cp_names;
$response["this_team"] = $this_team;
$response["cp_options"] = $cp_options;
$response["animal_locations"] = $animal_locations;
$response["teams"] = $teams;
$response["usernames"] = $usernames;
$response["game_state"] = [$game_state,$game_start,$game_end,$stage_time];
$response["inventory"] = [$players[$user_ID]["hand"]];
}
$response["live_scores"] = $final_results;

$response["debug_log"] = $debug_log;
echo json_encode($response);
