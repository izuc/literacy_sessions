<?php
//The purpose of this script is to fetch the statistic data from database via the object class formatted for google visualizations
session_start();
require_once('../classes/Field.class.php');
require_once('../classes/GraphData.class.php');
$user = User::getLoggedInUser();
if ($user != null AND (isset($_GET['start_date']) AND isset($_GET['end_date']))) {
	$data = GraphData::generate(date("Y-m-d", strtotime($_GET['start_date'])), date("Y-m-d", strtotime($_GET['end_date'])));
	$records = array();
	foreach($data as $statistic) {
		$records[] = array("id" => $statistic->getDateOccurred(),"cell" => $statistic->toArray());
	}
	echo json_encode(array("page" => 1, "total" => 1,"records" => sizeof($records), "rows" => $records));
}
?>