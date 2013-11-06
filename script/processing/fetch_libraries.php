<?php
//The purpose of this script is to fetch the libraries from the database via the object class
session_start();
require_once('../classes/Library.class.php');
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null AND ($user->isAdminUser())) {
	$libraries = Library::fetchList();
	$data = array();
	foreach($libraries as $library) {
		$data[] = array("id" => $library->getLibraryID(),"cell" => $library->toArray());
	}
	echo json_encode(array("page" => 1, "total" => 1,"records" => sizeof($data), "rows" => $data));
}
?>