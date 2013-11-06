<?php
//The purpose of this script is to save the statistic field selectbox values to the database via the object class
session_start();
require_once('../classes/FieldValue.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
	if (isset($_POST['oper'])) {
		$operation = $_POST['oper'];
		if ($operation === 'add'){
			$updatedFieldValue = new FieldValue($_POST['valueID'],$_GET['fieldID'],$_POST['libraryID'],$_POST['fieldValue']);
			$updatedFieldValue->insert();
		} elseif ($operation === 'edit'){
			$updatedFieldValue = new FieldValue($_POST['valueID'],$_POST['fieldID'],$_POST['libraryID'],$_POST['fieldValue']);
			$updatedFieldValue->update();
		} elseif ($operation === 'del'){
			FieldValue::delete($_POST['id']);
		}
	}
}
?>