<?php
//The purpose of this script is to fetch the side Control Panel toolbar
session_start();
require_once('../classes/User.class.php');
$user = User::getLoggedInUser();
if ($user != null) { 
	$user->loadMyPanel();
} else { 
	User::showLoginArea();
}
?>