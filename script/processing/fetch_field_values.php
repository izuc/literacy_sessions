<?php
//The purpose of this script is to fetch the field selectbox values from the database via the object class
session_start();
require_once('../classes/FieldValue.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser() AND (isset($_GET['field_id']) AND is_numeric($_GET['field_id'])))) {
	$fields = FieldValue::fetchAll($_GET['field_id']);
	$data = array();
	foreach($fields as $field) {
		$data[] = array("id" => $field->getValueID(),"cell" => $field->toArray());
	}
	echo json_encode(array("page" => 1, "total" => 1,"records" => sizeof($data), "rows" => $data));
}
?>