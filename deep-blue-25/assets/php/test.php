<?PHP
session_start();

if($_REQUEST["load"] == 1){
    $_SESSION['user_ID'] = 999;
    $_SESSION['location'] = 999;
    $_SESSION['game'] = 999;
}
session_write_close();  // Unlock session, allowing parallel requests
$cp = $_REQUEST["cp"];
if($cp > 0) {
    include 'script.php';
    };
if($_REQUEST["load"] == 1){
    echo "request landed";
} else {
include 'game.php';}