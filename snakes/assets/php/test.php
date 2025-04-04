<?PHP
session_start();

if($_REQUEST["load"] == 1){
    $_SESSION['user_ID'] = 999;
    $_SESSION['location'] = 1;
    $_SESSION['game'] = 995;
}

$cp = $_REQUEST["cp"];

if($cp > 0) {
include 'script.php';
};
include 'game.php';