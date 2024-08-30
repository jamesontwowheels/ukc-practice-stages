<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit;
}?>

<head>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src='/geo/targets.js'></script>
<script type="text/javascript" src='/geo/assets/js/distance.js'></script>
<link rel="stylesheet" href="/geo/assets/css/main.css">
</head>
<body>
  <h1>MINDGAMES</h1>
<div id="main">
<div id="timer"></div>

<div id="score" class="bucket">
  <h3>Running Score</h3>
  <div id="score_zone"></div>
</div>

<table id="checkpoints">
  <tbody>
   
    </tbody>
  </table>
<div id="inventory" class="bucket">
  <h3>Inventory</h3>
  <div id="inventory_zone"></div>  
  </div>
<div id="history" class="bucket">
  <h3>History</h3>
  <button id="toggleButton">Expand/Collapse</button>
  <div id="expandableContent" class="content">
  <ul id="commentary-list">
            <!-- Array items will be appended here -->
  </ul>
  </div>
</div>
</div>
</body>
<script type="text/javascript" src='/geo/assets/js/rows.js'></script>
<script type="text/javascript" src='/geo/assets/js/test.js'></script>