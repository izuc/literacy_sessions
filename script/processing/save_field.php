<?php
//The purpose of this script is to save the top level statistic fields to the database via the object class
session_start();
require_once('../classes/Field.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
	if (isset($_POST['oper'])) {
		$operation = $_POST['oper'];
		if ($operation === 'add') {
			$field = new Field(null, $_POST['field_label'], $_POST['field_type'], (($_POST['field_required'] == 'True')? 1 : 0));
			$field->insert();
		} elseif ($operation === 'edit'){
			$field = new Field($_POST['field_id'], $_POST['field_label'], $_POST['field_type'], (($_POST['field_required'] == 'True')? 1 : 0));
			$field->update();
		} elseif ($operation === 'del'){
			Field::delete($_POST['id']);
		}
	}
}
?>