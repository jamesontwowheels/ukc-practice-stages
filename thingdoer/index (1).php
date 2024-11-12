<!doctype html>
<html>
  <head>
    <title>Thindoer</title>
    <link rel="stylesheet" href="../app/assets/css/main.css" />
  </head>
  <body>
  <h1>Thingdoer Alpha Test</h1>
  
  <?PHP 
$user_id = $_GET["id"];
if ($user_id > 0){
include 'results_2.php'; }
else {
} ?>
  <button onclick="window.location.href='index.php?id=1';">Sam</button>
  <button onclick="window.location.href='index.php?id=2';">Player 2</button>
  <h2>You do not rise to the level of your goals. You fall to the level of your systems.</h2>
  <table class="weekly">
    <tr>
      <td><div class="blob blob_00"></div></td>
      <td><div class="blob blob_01"></div></td>
      <td><div class="blob blob_02"></div></td>
      <td><div class="blob blob_10"></div></td>
      <td><div class="blob blob_11"></div></td>
      <td><div class="blob blob_12"></div></td>
      <td><div class="blob blob_20"></div></td>
      <td><div class="blob blob_21"></div></td>
      <td><div class="blob blob_22"></div></td>
    </tr>
    <tr>
      <td><p>no goal set</p></td>
      <td><p>no goal set</p></td>
      <td><p>no goal set</p></td>
      <td><p>I will do</p></td>
      <td><p>I will do</p></td>
      <td><p>I will do</p></td>
      <td><p>not today</p></td>
      <td><p>not today</p></td>
      <td><p>not today</p></td>
      </tr>
    </tr>
    <tr>
      <td><p>no result set</p></td>
      <td><p>did it</p></td>
      <td><p>didn't do</p></td>
      <td><p>no result set</p></td>
      <td><p>did it</p></td>
      <td><p>didn't do</p></td>
      <td><p>no result set</p></td>
      <td><p>did it</p></td>
      <td><p>didn't do</p></td>
    </tr>
</table>
  </body>
</html>