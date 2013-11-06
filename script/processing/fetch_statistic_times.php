<?php
//The purpose of this script is to fetch the statistic times from database via the object class
session_start();
require_once('../classes/Statistic.class.php');
require_once('../classes/StatisticTime.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isSuperUser() || $user->isAdminUser()) AND (isset($_GET['statistic_id']) AND is_numeric($_GET['statistic_id']))) {
	$times = StatisticTime::fetchList($_GET['statistic_id']);
	$data = array();
	foreach($times as $time) {
		$data[] = array("id" => $time->getTimeID(), "cell" => $time->toArray());
	}
	echo json_encode(array("page" => 1, "total" => 1,"records" => sizeof($data), "rows" => $data));
}
?>