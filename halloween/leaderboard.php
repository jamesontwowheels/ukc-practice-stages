<?php
session_start();
if (!isset($_SESSION['location'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Critter Basket</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="assets/css/main.css?v0.1">
<link rel="stylesheet" href="assets/css/app-buttons.css?v0.1">
<link rel="stylesheet" href="assets/css/grid.css?v0.1">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<h1>Critter Basket</h1>

<div id="main">
  <div id="leaderboard" class="bucket">
    <div id="pic-grid"></div>
  </div>
  <div id="base-padding" class="bucket"></div>
</div>

<div id="footer">
  <div class="app-buttons">
    <a href="index.php" class="app-button" id="app1">Game</a>
    <a href="leaderboard.php" class="app-button" id="app2">Critters</a>
    <a href="history.php" class="app-button" id="app3">History</a>
  </div>
</div>

<div id="exit">
  <a href="index.php" id="app4"><i class="fas fa-arrow-left"></i></a>
</div>

<script>
$(document).ready(function() {
    const $grid = $('#pic-grid');
    $grid.empty();

    $.getJSON('assets/php/get_cp_grid.php', function(gridData) {
        gridData.forEach(src => {
            const $item = $('<div>').addClass('grid-item');
            if (src) {
                $('<img>').attr('src', src).appendTo($item);
            }
            $grid.append($item);
        });
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Failed to load grid:', textStatus, errorThrown);
        $('#pic-grid').text('Failed to load grid.');
    });
});
</script>


<script type="text/javascript" src='assets/js/app-buttons.js'></script>
</body>
</html>
