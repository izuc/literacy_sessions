<?php
//The purpose of this script is to manage user login attempts
session_start();
require_once('../classes/User.class.php');
if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case "login":
			$success = User::login($_POST['staff_account'], $_POST['account_password']);
			$title = (($success)?"Welcome ".strtoupper($_POST['staff_account']): "Login Failed");
			$message = (($success)?"Login Successful": "Please Try Again");
			break;
		case "logout":
			$success = User::logout();
			$title = "Goodbye";
			$message = "Logout Successful";
			break;
	}
	echo json_encode(array ('success'=>$success,'title'=>$title,'message'=>$message));
}
?>