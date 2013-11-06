<?php
//The purpose of this script is to fetch the data required to mask foreign key values in the tables
session_start();
require_once('../classes/Library.class.php');
require_once('../classes/SessionType.class.php');
require_once('../classes/User.class.php');
$loggedInUser = User::getLoggedInUser();
if ($loggedInUser != null AND ($loggedInUser->isAdminUser() || $loggedInUser->isSuperUser())) {
	if (isset($_GET['type'])) {
		$type = $_GET['type'];
		$data = array();
		if ($type === 'session'){
			$sessions = SessionType::fetchList();
			foreach($sessions as $session) {
				$data[] = array($session->getSessionID() => $session->getSessionName());
			}
		} elseif ($type === 'libraries'){
			$libraries = Library::fetchList();
			foreach($libraries as $library) {
				$data[] = array($library->getLibraryID() => $library->getLibraryName());
			}
		} elseif ($type === 'users'){
			$users = User::fetchList();
			foreach($users as $user) {
				$data[] = array($user->getUserID() => $user->getUserStaffAccount());
			}
		}
		echo str_replace(array('[',']'), '', str_replace('},{', ',', json_encode($data)));
	}
}
?>