<?php
//The purpose of this script is to fetch the statistic data from the database via the object class
session_start();
require_once('../classes/Statistic.class.php');
require_once('../classes/StatisticData.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isSuperUser() || $user->isAdminUser()) AND (isset($_GET['statistic_id']) AND is_numeric($_GET['statistic_id']))) {
	$statistic_data = StatisticData::fetchList($_GET['statistic_id']);
	$records = array();
	foreach($statistic_data as $data) {
		$records[] = array("id" => $data->getDataID(), "cell" => $data->toArray());
	}
	echo json_encode(array("page" => 1, "total" => 1,"records" => sizeof($records), "rows" => $records));
}
?>