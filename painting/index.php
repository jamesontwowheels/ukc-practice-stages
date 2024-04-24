<!DOCTYPE HTML>
<!--
	Hyperspace by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Painting</title>
		<link rel="stylesheet" href="assets/css/plain.css" />
		
		
	</head>
	<body class="is-preload">

	<?PHP include('api_test.php'); 
	?>
		<!-- Sidebar -->
<h1>Manchester Painting</h1>

		<!-- Wrapper -->
			<div id="wrapper">

				
						<div class="content">
								<div id="detailed_results" class="inner">

								<table id='results' class='display'>
							<thead>
								<tr>
									<th onclick="sortTable(0)">Name</th>
									<th onclick="sortTable(1)">Surname</th>
									<th onclick="sortTable(2)">Time</th>
									<th onclick="sortTable(3)">Score</th>
									<th onclick="sortTable(4)">Time Penalty</th>
									<th onclick="sortTable(5)">Final Score</th>
									<th>detailed results</th>
				
								</tr>
							</thead>
							<tbody>
						<?PHP
						while($r < count($results_summary)){
							echo "<tr>";
								$rd = 0;
								while($rd<6){
									echo "<td>".$results_summary[$results_ids[$r]][0][$rd]."</td>";
									$rd += 1;
								}
								echo "<td><a href='#".$results_ids[$r]."' class='button scrolly'>detail</a>";
								echo "</tr>";
								$r += 1;
						}?>
							</tbody>
						</table>
								</div>
							</div>

				<!-- Two -->
				
						<div class="inner"> 
							
						<?PHP
						$rd = 0;
						//for each person
						while($rd < count($results_ids)){
					
							$id = $results_ids[$rd];
							$id_count = count($results_detailed[$id]);
							$idc = 0;

							echo "<div id=".$id.">";
							echo "<h3>".$results_names[$id][0]." ".$results_names[$id][1]."</h3>";
							echo "<table><thead><tr><th>time</th><th>CP</th><th>action</th><th>score</th><th>running total</th></tr></thead>";

							//for each action
							while ($idc < $id_count){
								$col = 0;
								$col_time = $results_detailed[$id][$idc][0];
								$col_mins = floor($col_time/60);
								$col_secs = $col_time - $col_mins *60;
								$col_print_time = $col_mins."m ".$col_secs."s";
								$col = 1;

								echo "<tr>";
								echo "<td>$col_print_time</td>";
								//for each detail
								while ($col < 5){

									echo "<td>".$results_detailed[$id][$idc][$col]."</td>";
									$col += 1;
								}
								$idc += 1;

								echo "</tr>";
							}

							echo "</table></div>";
							
							$rd += 1;
						}?>
						</div>

				<!-- Three -->

			</div>

		<!-- Scripts -->
		<script>function sortTable(col) {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.getElementById("results");
        switching = true;
        while (switching) {
          switching = false;
          rows = table.rows;
          for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("td")[col];
            y = rows[i + 1].getElementsByTagName("td")[col];
            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
              shouldSwitch = true;
              break;
            }
          }
          if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
          }
        }
      }</script>		

	</body>
</html>