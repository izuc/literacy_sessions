<?php
//The purpose of this script is to fetch the user data from database via the object class
session_start();
require_once('../classes/User.class.php');
$loggedInUser = User::getLoggedInUser();
if ($loggedInUser != null AND ($loggedInUser->isAdminUser())) {
	$users = User::fetchList();
	$data = array();
	foreach($users as $user) {
		$data[] = array("id" => $user->getUserID(),"cell" => $user->toArray());
	}
	echo json_encode(array("page" => 1, "total" => 1,"records" => sizeof($data), "rows" => $data));
}
?>