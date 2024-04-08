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
    //Event Bulk CPs ***EDIT THIS***
    $cp_ghosts = [11,12,13,14,15];
    $cp_pills = [21,22];
    $cp_bites = [31,32,33,34,35,36,37,38];
    
    //Event reference information ***EDIT THIS***
    $level_ghosts = [[1,1,0,0,0],[1,1,1,0,0],[1,1,1,1,0],[1,1,1,0,1],[1,1,0,1,1]];
    $level_points = [0,1,2,3,4,5];
    $level_pill_power = [0,90,5,3,2,1];

    //Event Special CPs ***EDIT THIS***
    $cp_level_up = 77;
    $cp_start_finish = 999;

    //values ***EDIT THIS***
    $stage_time = 75*60;
    $penalty_rate = 3; 
    //seconds per point lost

    //BAU Bulk CPs
    $cps_teams = [401,402,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440];
    
    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];

    //player/team catcher
    $players = [];
    $teams = [];
      
    
    echo "55";

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
            $time_penalty = floor(($finish_time-$stage_time)/$penalty_rate);
        } else {$time_penalty = 0;}
    $x += 1;

//set-up course/result variables for each contestants
    
    $id = $result['Id'];  

    $count_cps = count($result['Punches']);
    $y = 0;
    $cps = [];
    $times = [];

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
            $next_team += 1;}

//create the player:
$player = [$name,$surname,$cps,$times,$team,$time_penalty,$start_time];
$players[] = $player;
}


echo "110";
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

echo "148";

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

    //player specific event ***EDIT THIS***
    $current_level = 1;
    $target_ghosts = [[0],[0,1,1,0,0,0],[0,1,1,1,0,0],[0,1,1,1,1,0],[0,1,1,1,0,1],[0,1,1,0,1,1]];
    $powerup = 0;
    $eaten_bites = [];

    // cycle through the punch list;
    $z = 0;
    echo "168";
    
    while ($z < count($cps)){
        // add to detailed results = $results_detailed[$id][] = [_your code_];
        // add to summary results = $results_summary[$id][] = [_your code_];

        $cp = $cps[$z];
        $t = $times[$z];
        $z += 1;

        echo "184 ";
        echo $cp;
        //insert Checkpoint rules here ***EDIT THIS***

        //collect pill
        if(in_array($cp,$cp_pill)){
            $powerup = $t + $level_pill_power[$current_level]*60;
            $results_detailed[$id][] = [$t,$cp,"Pill eaten - powered up until $t",0,$running_score];
        }

        //eat ghost
        if(in_array($cp,$cp_ghosts)){
            $ghost = $cp-10;
            if($target_ghosts[$current_level][$ghost] == 0){
                $results_detailed[$id][] = [$t,$cp,"Ghost sleeping or already eaten",0,$running_score];
            } else {
                if($powerup > $t){
                    $target_ghosts[$current_level][$ghost] = 0;
                    $award = $level_points[$current_level]*10;
                    $running_score += $award;
                    $results_detailed[$id][] = [$t,$cp,"Ghost $ghost eaten",$award,$running_score];
                } else {
                    $award = -$level_points[$current_level]*20;
                    $running_score += $award;
                    $results_detailed[$id][] = [$t,$cp,"Oh no! You were caught by Ghost $ghost",$award,$running_score];
                }
            }
        }

        //eat byte
        if(in_array($cp,$cp_bites)){
            if(in_array($cp,$eaten_bites)){
                //noaction
            } else {
                $eaten_bites[] = $cp;
                $award = $level_points[$current_level];
                $running_score += $award;
                $results_detailed[$id][] = [$t,$cp,"Yum, bite $cp eaten",$award,$running_score];
            }
        }

        //level-up
        if($cp == $cp_level_up){
            if(array_sum($target_ghosts[$current_level]) == 0){
                $powerup = $t;
                $eaten_bites = [];
                $current_level += 1;
                $results_detailed[$id][] = [$t,$cp,"Level up! Now on Level $current_level","-",$running_score];
            } else {
                $results_detailed[$id][] = [$t,$cp,"Level up failed, there are still ghosts out there","-",$running_score];
            }
        }
                
    }

    //can put some finish line rules in here
    echo "something here";
    $final_score = $running_score - $time_penalty;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];

      $e += 1; 
}

$r = 0;

?>