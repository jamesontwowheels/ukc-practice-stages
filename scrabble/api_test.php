<?PHP
ini_set("allow_url_fopen", 1);
//Get event results using maprun API:
$event_name = "newcastle%20scrabble";
$api_url = "https://p.fne.com.au:8886/resultsGetPublicForEvent?eventName=";
$url_live = $api_url . $event_name;
//commented out for local testing:
//file_put_contents("results.json", file_get_contents($url_live)); 
//$url_live = "results.json";
// this saves the file locally, so can use for caching results... 
$obj = json_decode(file_get_contents($url_live), true);
$count_results = count($obj['results']);
$results = $obj['results'];
$x = 0;
//set-up the static constants (each requires it's own rule...):
    //Bulk CPS
    $words = ["MACHINE","ANEMIC","CHAINE","HAEMIC","HAEMIN","HEMINA","ICEMAN","MANCHE","AMICE","AMINE","AMNIC","ANIME","CANEH","CHAIN","CHINA","CHIME","ENIAC","CHINE","HANCE","HEMIC","HEMIN","MACHE","MACHI","MANEH","MANIC","MICHE","MINAE","MINCE","NACHE","NICHE","ACHE",
  "ACME",
  "ACNE",
  "AHEM",
  "AINE",
  "AMEN",
  "AMIE",
  "AMIN",
  "ANCE",
  "CAIN",
  "CAME",
  "CAMI",
  "CANE",
  "CHAI",
  "CHAM",
  "CHEM",
  "CHIA",
  "CHIN",
  "CINE",
  "EACH",
  "EINA",
  "EMIC",
  "HAEM",
  "HAEN",
  "HAIN",
  "HAME",
  "INCH",
  "MACE",
  "MACH",
  "MAIN",
  "MANE",
  "MANI",
  "MEAN",
  "MECH",
  "MEIN",
  "MICA",
  "MICE",
  "MICH",
  "MIEN",
  "MIHA",
  "MINA",
  "MINE",
  "NACH",
  "NAME",
  "NEMA",
  "NICE","ACE",
  "ACH",
  "AHI",
  "AIM",
  "AIN",
  "AME",
  "AMI",
  "ANE",
  "ANI",
  "CAM",
  "CAN",
  "CHA",
  "CHE",
  "CHI",
  "EAN",
  "ECH",
  "HAE",
  "HAM",
  "HAN",
  "HEM",
  "HEN",
  "HIC",
  "HIE",
  "HIM",
  "HIN",
  "ICE",
  "ICH",
  "MAC",
  "MAE",
  "MAN",
  "MEH",
  "MEN",
  "MIC",
  "MNA",
  "NAE",
  "NAH",
  "NAM",
  "NIE",
  "NIM"];
    $cps_letters = [1,2,3,4,5,6,7];
    $cps_bonus = [11,12];
    $word = ["","A","I","C","H","E","N","M"];
    $word_value = [0,1,1,3,4,1,1,3];
    $word_length_value = [0,0,0,0,4,8,13,20];
    $word_count_bonus = [0,0,0,5,10,20,30,45,60,80,100];

    //special CPS;
    $cp_wsf = 20;
    $cp_start_finish = 999;

    //results catchers
    $results_detailed = [];
    $results_summary = [];
    $results_ids= [];
    $results_names= [];
    //values
    $stage_time = 75*60;

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
    $used_bonuses = [];
    $used_letters = [];
    $used_words = [];
    $current_word = "";
    $current_word_value = 0;
    $current_bonus = 1;
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

        //pick up letter - start playing CPs 1-7
        if(in_array($cp,$cps_letters)){
            if(in_array($cp,$used_letters)){
                //letter used in word
                $results_detailed[$id][] = [$t,$cp,"letter $cp already used",0,$running_score];
            } else {
                //add to word
                $letter = $word[$cp];
                $current_word = $current_word.$word[$cp];
                $current_word_value += $word_value[$cp];
                $used_letters[] = $cp;
                $results_detailed[$id][] = [$t,$cp,"$letter collected. word = $current_word","",$running_score];
            }
        }

        //pick up bonus 
        if(in_array($cp,$cps_bonus)){
            if(in_array($cp,$used_bonuses)){
                //bonus already played
                $results_detailed[$id][] = [$t,$cp,"bonus $cp already used","",$running_score];
            } elseif ($current_bonus > 1.5) {
                //other bonus already in play
                $used_bonuses[] = $cp;
                $results_detailed[$id][] = [$t,$cp,"bonus $cp invalid, $current_bonus bonus already in use.","",$running_score];
            } else {
                //award bonus
                $used_bonuses[] = $cp;
                $current_bonus = $cp - 9;
                $results_detailed[$id][] = [$t,$cp,"bonus $current_bonus collected.","",$running_score];
            }
        }

        //play word
        if($cp==$cp_wsf){
            if(in_array($current_word,$words)){
                if(in_array($current_word,$used_words)){
                $results_detailed[$id][] = [$t,$cp,"$current_word played, already used.","",$running_score];
                } else {
                $value = ($word_length_value[strlen($current_word)] + $current_word_value) * $current_bonus;
                $running_score += $value;
                $used_words[] = $current_word;
                $results_detailed[$id][] = [$t,$cp,"$current_word successfully played!","+ $value",$running_score];
                }
            } else {
                $results_detailed[$id][] = [$t,$cp,"$current_word played, but not a known word","",$running_score];
            }
            $current_word = "";
            $current_bonus = 1;
            $current_word_value = 0;
            $used_letters = [];
            $value = 0;
        }

        //start_finish

        //

    }

    $words_found = count($used_words);
    $wf_bonus = $word_count_bonus[$words_found];
    $running_score += $wf_bonus;
    $results_detailed[$id][] = [$t,$cp,"$words_found words found, + $wf_bonus bonus","",$running_score];

    $final_score = $running_score - $time_penalty;
   $results_summary[$id][] = [$name,$surname,$time,$running_score,-$time_penalty,$final_score,$id];
}

$r = 0;

?>