<?php
//The purpose of this script is to save the library values to the database via the object class
session_start();
require_once('../classes/Library.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
	if (isset($_POST['oper'])) {
		$operation = $_POST['oper'];
		if ($operation === 'add'){
			$library = new Library(null, $_POST['library_name']);
			$library->insert();
		} elseif ($operation === 'edit'){
			$library = new Library($_POST['library_id'], $_POST['library_name']);
			$library->update();
		} elseif ($operation === 'del'){
			Library::delete($_POST['id']);
		}
	}
}
?>