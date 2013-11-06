<?php
//The purpose of this script is to fetch the top level fields from the database via the object class
session_start();
require_once('../classes/Field.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
	$fields = Field::fetchList();
	$data = array();
	foreach($fields as $field) {
		$data[] = array("id" => $field->getFieldID(),"cell" => $field->toArray());
	}
	echo json_encode(array("page" => 1, "total" => 1,"records" => sizeof($data), "rows" => $data));
}
?>