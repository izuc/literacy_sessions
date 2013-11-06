<?php
//The purpose of this script is to save the users to the database via the object class
session_start();
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
	if (isset($_POST['oper'])) {
		$operation = $_POST['oper'];
		if ($operation === 'add'){
			$user = new User(null, strtoupper($_POST['staff_account']), $_POST['library_id'], $_POST['account_password'], $_POST['account_type']);
			$user->insert();
		} elseif ($operation === 'edit'){
			$user = new User($_POST['user_id'], strtoupper($_POST['staff_account']), $_POST['library_id'], $_POST['account_password'], $_POST['account_type']);
			$user->update();
		} elseif ($operation === 'del'){
			if ($user->getUserID() != $_POST['id']) {
				User::delete($_POST['id']);
			}
		}
	}
}
?>