<?PHP
ini_set("allow_url_fopen", 1);
//Get event results using maprun API:
$event_name = "painting-htp%20SCOREQ75%20PZ";
$api_url = "https://p.fne.com.au:8886/resultsGetPublicForEvent?eventName=";
$url_live = $api_url . $event_name;
//commented out for local testing:
// full_url = https://p.fne.com.au:8886/resultsGetPublicForEvent?eventName=Uno%20SCOREQ90%20PZ
//file_put_contents("results.json", file_get_contents($url_live)); 
//$url_live = "results.json";
// this saves the file locally, so can use for caching results... 
$obj = json_decode(file_get_contents($url_live), true);
$count_results = count($obj['results']);
$results = $obj['results'];
$x = 0;
$next_team = 500;

// prefix for QR codes: http://www.maprunners.com.au?c=
// PIN for event is 1756

//set-up the static constants (each requires it's own rule...):
    //Bulk CPS
    $cp_canvases = [1,2,3,4,5];
    $cp_paints = [11,12,13,14];
    $cp_brushes = [21,22,23,24,25];
    $cp_signatures = [51,52,53,54,55];
    
    $canvas_names = ["","Japan","France","Algeria","Argentina","Azerbaijan"];
    $paint_names = ["","Red","Yellow","Blue","White","Green","Light Blue","Brown",];
    $target_flags = [[],[1,0,0,4,0],[0,1,0,3,4],[1,1,5,4,0],[4,2,6,0,6],[1,4,6,4,5]];
    $flag_difficult = [0,1,1.5,2,2,2.5];
    $flag_scores = [0,10,15,20,20];
    $flag_grade = ["F","D","C","B","B"];


        //Teams
        $cps_teams = [401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440];

      


    //special CPS;
    $cp_mix = 77;
    $cp_start_finish = 999;
    
    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];

    //player/team catcher
    $players = [];
    $teams = [];
    
    //values
    $stage_time = 75*60;


    
//start looping the contestants:
while($x < $count_results){
    $result = $results[$x];
    $team = 0;
    
    //work out start time in seconds:
        $start_local = $result['StartPunchTimeLocal'];
        $start_hours = intval(substr($start_local,0,2));
        $start_minutes = intval(substr($start_local,3,2));
        $start_seconds = intval(substr($start_local,6,2));
        $start_time = (($start_hours * 60 + $start_minutes) * 60 + $start_seconds);
    
    $name = $result['Firstname'];
    $surname = $result['Surname'];
    $finish_time = intval($result['TotalTimeSecs']);
    //check for time penalties:
        if($finish_time > $stage_time){
            $time_penalty = floor(($finish_time-$stage_time)/3);
        } else {$time_penalty = 0;}
    $x += 1;

//set-up course/result variables for each contestants
    
    $id = $result['Id'];  

    $count_cps = count($result['Punches']);
    $y = 0;
    $cps = [];
    $times = [];
// $solved_puzzles = [];
//  $played_cards = [];

//build and order the punches list:
    while ($y < $count_cps){
        $new_cp = preg_replace("/[^0-9]/", "",$result['Punches'][$y]["ControlId"]);
        $cps[] = $new_cp;
        $times[] = intval($result['Punches'][$y]["TimeAfterStartSecs"]) + $start_time;
        $pr = $player_results = [];
        $y += 1;
    }
    array_multisort($times, $cps);
$b = 0;
while ($b < count($cps)){
    if(in_array($cps[$b],$cps_teams)){
        $team = $cps[$b];
    } else {};
    $b += 1;
}

if($team > 1){ //all good
} else { $team = $next_team;
    echo $next_team;
            $next_team += 1;}

//create the player:
$player = [$name,$surname,$cps,$times,$team,$time_penalty,$start_time];
$players[] = $player;
}


//build teams
$c = 0;
$teams_used = [];
while($c < count($players)){
    $d = $players[$c];
    $d_team = $d[4];
    if(in_array($d_team,$teams_used)){
        $d_name = $d[0]." ".substr($d[1],0,1);
        $d_cps = $d[2];
        $d_times = $d[3];
        $d_penalty = $d[5];
        $d_start = $d[6];

        //$e = existing
        $e = $teams[$d_team];
        $teams[$d_team][0] = $e[0]." & ".$d_name; 
        $teams[$d_team][1] = array_merge($e[1],$d_cps);        
        $teams[$d_team][2] = array_merge($e[2],$d_times);        
        $teams[$d_team][3] = max($e[3],$d_penalty);       
        $teams[$d_team][4] = min($e[4],$d_start);
        array_multisort($teams[$d_team][2],$teams[$d_team][1]);
    } else { 
        $teams_used[] = $d_team;
        $d_name = $d[0]." ".substr($d[1],0,1);
        $d_cps = $d[2];
        $d_times = $d[3];
        $d_penalty = $d[5];
        $d_start = $d[6];
        $teams[$d_team] = [$d_name,$d_cps,$d_times,$d_penalty,$d_start];
     }
     $c += 1;
}


/////going to need to cycle through players here, and drag down the results collectors!!!////
//$e = each team
$e = 0;

while($e < count($teams_used)){
    $id = $teams_used[$e];
    $result = $teams[$id];
    $cps = $result[1];
    $times = $result[2];
    $time_penalty = $result[3];
    $results_ids[] = $id;
    $name = $result[0];
    $team_start = $result[4];
    $results_names[$id][0] = $result[0];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    $running_score = 0;

  

    //player specific
    $used_canvases = [0,0,0,0,0,0];
    $used_signatures = [];
    $signed_canvases = [0,0,0,0,0,0];
    $flags = [[],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0]];
    $current_canvas = 0;
    $current_canvas_name = "";
    $current_paint = 0;
    $current_paint_name = "";
    $current_mix = false;

    // cycle through the punch list;
    $z = 0;
    
    while ($z < count($cps)){
          // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp = $cps[$z];
        $t = $times[$z];
        $z += 1;

        //paint an area
        if(in_array($cp,$cp_brushes)){
            $brush = $cp - 21;
            $brush_name = $brush + 1;
            if($current_canvas == 0){
                $results_detailed[$id][] = [$t,$cp,"brush used, but no canvas held",0,$running_score];
            } elseif ($current_paint == 0){
                $results_detailed[$id][] = [$t,$cp,"brush used, but no paint held",0,$running_score];
            } else {
                $flags[$current_canvas][$brush] = $current_paint;
                $results_detailed[$id][] = [$t,$cp,"$current_canvas_name section $brush_name painted $current_paint_name",0,$running_score];
            }
        }

        if(in_array($cp,$cp_canvases)){
            $current_canvas = $cp;
            $current_canvas_name = $canvas_names[$cp];
            $used_canvases[$cp] = 1;
            $results_detailed[$id][] = [$t,$cp,"$current_canvas_name picked up",0,$running_score];
        }

        if(in_array($cp,$cp_paints)){
            $paint = $cp - 10;
            if($current_mix == true){
                if($current_paint == 3 && $paint == 4){
                    $current_paint = 6;
                } elseif ($current_paint == 4 && $paint == 3) {
                    $current_paint = 6;
                } elseif ($current_paint == 3 && $paint == 2) {
                    $current_paint = 5;
                } elseif ($current_paint == 2 && $paint == 3){
                    $current_paint = 5;
                } else {
                    $current_paint = 7;
                }
                $current_paint_name = $paint_names[$current_paint];
                $current_mix = false;
                $results_detailed[$id][] = [$t,$cp,"New colour mixed: $current_paint_name",0,$running_score];
            } else {
                $current_paint = $paint;
                $current_paint_name = $paint_names[$current_paint];
                $results_detailed[$id][] = [$t,$cp,"$current_paint_name picked up",0,$running_score];
            }
        }

        if($cp == $cp_mix){
            $current_mix = true;
            $results_detailed[$id][] = [$t,$cp,"Mix point visited",0,$running_score];
        }

        if(in_array($cp,$cp_signatures)){
            if($current_canvas == 0){
                $results_detailed[$id][] = [$t,$cp,"signature attempted, but no canvas held",0,$running_score];
            } else {
                if(in_array($cp,$used_signatures)){
                $results_detailed[$id][] = [$t,$cp,"puzzle already used",0,$running_score];
                } else {
                    $signed_canvases[$current_canvas] = 1;
                    $results_detailed[$id][] = [$t,$cp,"$current_canvas_name signed",0,$running_score];
                    $used_signatures[] = $cp;
                }       
            }
        }
        //start_finish

        //

    }
echo $z;
    $j = 1;

    //check each flag
    while($j < 6){
        
        if($target_flags[$j] === $flags[$j]){
                $points = 30;
                $grade = "A";
        } 
        else {
            $g = 0; //paint area
            $h = 0; //successes
            $canvas_score = $used_canvases[$j] * 5;
            while ($g < 5){
                if ($target_flags[$j][$g] > 0 && $target_flags[$j][$g] === $flags[$j][$g]){
                    $h += 1;
                }
                $g += 1;
            }
            $points = $flag_scores[$h] + $canvas_score;
            $grade = $flag_grade[$h];
        }

        if($signed_canvases[$j] == 1){
                $signature_multiplier = 1.2;
                $signed = "signed";
            } else {
                $signature_multiplier = 1;
                $signed = "";
            }

        $points = $points * $flag_difficult[$j] * $signature_multiplier;
        $running_score += $points;
        $flag_name = $canvas_names[$j];
        $results_detailed[$id][] = [$t,"End","$flag_name flag $signed - Grade $grade - $points points","",$running_score];

        $j +=1;
        $h = 0;
    }

    $final_score = $running_score - $time_penalty;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];

      $e += 1; 
}

$r = 0;

?>