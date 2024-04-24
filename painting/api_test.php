<?PHP
ini_set("allow_url_fopen", 1);
//Get event results using maprun API:
$event_name = "MCR-painting%20SCOREQ60%20PZ";
$api_url = "https://p.fne.com.au:8886/resultsGetPublicForEvent?eventName=";
$url_live = $api_url . $event_name;
//commented out for local testing:
//file_put_contents("results.json", file_get_contents($url_live)); 
// $url_live = "results.json";
// this saves the file locally, so can use for caching results... 
$obj = json_decode(file_get_contents($url_live), true);
$count_results = count($obj['results']);
$results = $obj['results'];
$x = 0;
//set-up the static constants (each requires it's own rule...):
    //Bulk CPS
    $cp_canvases = [1,2,3,4,5];
    $cp_paints = [11,12,13,14];
    $cp_brushes = [21,22,23,24,25];
    
    $canvas_names = ["","Japan","France","Algeria","Argentina","Azerbaijan"];
    $paint_names = ["","Red","Yellow","Blue","White","Green","Light Blue","Brown",];
    $target_flags = [[],[1,0,0,4,0],[0,1,0,3,4],[1,1,5,4,0],[4,2,6,0,6],[1,4,6,4,5]];
    $flag_difficult = [0,1,1.5,2,2,2.5];
    $flag_scores = [0,10,15,20,20];
    $flag_grade = ["F","D","C","B","B"];
// red = 1 = cp11, yellow = 2 = cp12, blue = 3 = cp13, white = 4 = cp4, green = 5 (mixed), light blue = 6 (mixed)

    //special CPS;
    $cp_signature = 50;
    $cp_mix = 77;
    $cp_start_finish = 999;

    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    //values
    $stage_time = 60*60;

//start looping the contestants:
while($x < $count_results){
    $result = $results[$x];
    $name = $result['Firstname'];
    $surname = $result['Surname'];
    $finish_time = intval($result['TotalTimeSecs']);
    //check for time penalties:
        if($finish_time > $stage_time){
            $time_penalty = floor(($finish_time-$stage_time)/5);
        } else {$time_penalty = 0;}
    $x += 1;
//set-up course/result variables for each contestants
    $id = $result['Id'];
    $results_ids[] = $id;
    $results_names[$id] = [$name,$surname];
    $results_detailed[$id] = [];
    $results_summary[$id] = [];
    $count_cps = count($result['Punches']);
    $y = 0;
    $cps = [];
    $times = [];
    $used_canvases = [0,0,0,0,0,0];
    $signed_canvases = [0,0,0,0,0,0];
    $flags = [[],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0]];
    $current_canvas = 0;
    $current_canvas_name = "";
    $current_paint = 0;
    $current_paint_name = "";
    $current_mix = false;

    $running_score = 0;

//build and order the punches list:
    while ($y < $count_cps){
        $cps[] = preg_replace("/[^0-9]/", "",$result['Punches'][$y]["ControlId"]);
        $times[] = intval($result['Punches'][$y]["TimeAfterStartSecs"]);
        $pr = $player_results = [];
        $y += 1;
    }
    array_multisort($times, $cps);

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

        if($cp == $cp_signature){
            if($current_canvas == 0){
                $results_detailed[$id][] = [$t,$cp,"signature attempted, but no canvas held",0,$running_score];
            } else {
                $signed_canvases[$current_canvas] = 1;
                $results_detailed[$id][] = [$t,$cp,"$current_canvas_name signed",0,$running_score];
            }
        }
        //start_finish

        //

    }

    $j = 1;

    //check each flag
    while($j < 6){
        
        if($target_flags[$j] === $flags[$j]){
            if($signed_canvases[$j] == 1){
                $points = 40;
                $grade = "A+";
            } else {
                $points = 30;
                $grade = "A";
            }
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

        $points = $points * $flag_difficult[$j];
        $running_score += $points;
        $flag_name = $canvas_names[$j];
        $results_detailed[$id][] = [$t,"End","$flag_name flag - Grade $grade - $points points","",$running_score];

        $j +=1;
        $h = 0;
    }

    $final_score = $running_score - $time_penalty;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
}

$r = 0;

?>