<?php
//The purpose of this script is to fetch the sessions from the database via the object class
session_start();
require_once('../classes/SessionType.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
	$sessions = SessionType::fetchList();
	$data = array();
	foreach($sessions as $session) {
		$data[] = array("id" => $session->getSessionID(),"cell" => $session->toArray());
	}
	echo json_encode(array("page" => 1, "total" => 1,"records" => sizeof($data), "rows" => $data));
}
?>