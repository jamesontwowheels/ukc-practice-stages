<!doctype html>
<html>
  <head>
    <title>Thingdoer</title>
    <link rel="stylesheet" href="../app/assets/css/main_2023.css" />
    <link rel="apple-touch-icon" sizes="128x128" href="../app/assets/img/icon.png">
<script src="https://www.gstatic.com/charts/loader.js"></script>
  </head>
  <body>
  <h1>Thingsdoing</h1>
 
  <div class="card">
<?
include 'results_wake.php';
?>
</div>  

<div class="card">
  <?PHP 
$user_id = $_GET["id"];
if (1){
include 'results_pomodoro.php'; }
else {
} 
?>


<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

// Set Data
const data = google.visualization.arrayToDataTable([
  ['Contry', 'Mhl'],
  ['pass',<?PHP echo $j_all; ?>],
  ['fail',<?PHP echo $k_all; ?>],
]);

// Set Options
const options = {
  colors: ['#d3f3f1', '#f27a7d'],
  pieHole: 0.4,
  backgroundColor: { fill:'transparent' },
  legend: {position: 'none'}
};

// Draw
const chart = new google.visualization.PieChart(document.getElementById('myChart'));
chart.draw(data, options);

}
</script>
</div>
  </body>
</html>