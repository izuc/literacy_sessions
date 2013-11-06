<?php
//The purpose of this script is to fetch the top level statistics from database via the object class
session_start();
require_once('../classes/Statistic.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isSuperUser() || $user->isAdminUser())) {
	$library_id = ((isset($_GET['library_id']) AND is_numeric($_GET['library_id']))? $_GET['library_id'] : $user->getLibraryID());
	$statistics = Statistic::fetchList($library_id);
	$data = array();
	foreach($statistics as $statistic) {
		$data[] = array("id" => $statistic->getStatisticID(), "cell" => $statistic->toArray());
	}
	echo json_encode(array("page" => 1, "total" => 1,"records" => sizeof($data), "rows" => $data));
}
?>