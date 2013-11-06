<?php
// The purpose of this script is to generate a graph using the google visualization plugin for the summarised statistic data.
session_start();
require_once('../classes/User.class.php');
require_once('../classes/GraphData.class.php');
$user = User::getLoggedInUser();
if ($user != null AND (isset($_GET['start_date']) AND isset($_GET['end_date']))) {
?>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
		google.load('visualization', '1', {'packages':['annotatedtimeline']});
		google.setOnLoadCallback(drawChart);
		function drawChart() {     
			var data = new google.visualization.DataTable();
			data.addColumn('date', 'Date'); 
			data.addColumn('number', 'Sessions per Day');
			data.addColumn('number', 'Students per Day');
			data.addRows([ 
				<?php
					$data = GraphData::generate(date("Y-m-d", strtotime($_GET['start_date'])), date("Y-m-d", strtotime($_GET['end_date'])));
					foreach($data as $statistic) {
						echo "[new Date(" . date("Y,m,d", strtotime($statistic->getDateOccurred())) . "), " . $statistic->getAmountSessions()  . ", " . $statistic->getAmountStudents() . "]"
						. (!($statistic == end($data)) ? ',' : '');
					}
				?>
			]);
			var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div')); 
			chart.draw(data, {displayAnnotations: true, wmode:'transparent'});
		}
	</script>
	<div id='chart_div' style='width: 640px; height: 300px;'></div>
<?php
}
?>