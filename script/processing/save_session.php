<?php
//The purpose of this script is to save session data to the database via the object class
session_start();
require_once('../classes/SessionType.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
	if (isset($_POST['oper'])) {
		$operation = $_POST['oper'];
		if ($operation === 'add'){
			$session = new SessionType(null, $_POST['session_name']);
			$session->insert();
		} elseif ($operation === 'edit'){
			$session = new SessionType($_POST['session_id'], $_POST['session_name']);
			$session->update();
		} elseif ($operation === 'del'){
			SessionType::delete($_POST['id']);
		}
	}
}
?>