<?php
//The purpose of this script is to save the statistic field library and session availability to the database via the object class
session_start();
require_once('../classes/FieldAvailable.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
	if (isset($_POST['oper'])) {
		$operation = $_POST['oper'];
		if ($operation === 'add'){
			$fieldAvailable = new FieldAvailable($_POST['available_id'],$_POST['library_id'],$_POST['session_id'],$_GET['field_id']);
			$fieldAvailable->insert();
		} elseif ($operation === 'edit'){
			$fieldAvailable = new FieldAvailable($_POST['available_id'],$_POST['library_id'],$_POST['session_id'],$_POST['field_id']);
			$fieldAvailable->update();
		} elseif ($operation === 'del'){
			FieldAvailable::delete($_POST['id']);
		}
	}
}
?>