<?PHP
session_start();

if($_REQUEST["load"] == 1){
    $_SESSION['user_ID'] = 999;
    $_SESSION['location'] = 999;
    $_SESSION['game'] = 999;
}

$cp = $_REQUEST["cp"];

if($cp > 0) {
include 'script.php';
};
if($_REQUEST["load"] == 1){
} else {
include 'game.php';}