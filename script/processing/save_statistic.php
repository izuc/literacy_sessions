<?php
//The purpose of this script is to save the inputted statistics to the database via the object class
session_start();
require_once('../classes/User.class.php');
require_once('../classes/Statistic.class.php');
require_once('../classes/StatisticData.class.php');
require_once('../classes/StatisticTime.class.php');
$user = User::getLoggedInUser();
if ($user != null) {
	if (isset($_POST['oper'])) {
		$operation = $_POST['oper'];
		if ($operation === 'add') {
			if (isset($_POST['date']) AND isset($_POST['session']) AND isset($_POST['occurrence']) AND (isset($_POST['starttime1']) AND isset($_POST['endtime1']) AND isset($_POST['attendees1']))) {
				$statistic = new Statistic(null, $user->getLibraryID(), $_POST['session'], $user->getUserID(), null, date('Y-m-d', strtotime($_POST['date'])));
				$statistic_id = $statistic->insert();
				
				for ($i = 1; $i <= $_POST['occurrence']; $i++) {
					if (isset($_POST['starttime' . $i]) AND isset($_POST['endtime' . $i]) AND isset($_POST['attendees' . $i])) {
						$time = new StatisticTime(null, $statistic_id, $_POST['attendees' . $i], $_POST['starttime' . $i], $_POST['endtime' . $i]);
						$time->insert();
					}
				}
				
				foreach($_POST as $key => $data) {
					$field = explode('_', $key);
					if (sizeof($field) == 2) {
						$statistic_data = new StatisticData(null, $statistic_id, $field[1], null, null);
						if ($field[0] == 'selectbox') {
							$statistic_data->setValueID($data);
						} else {
							$statistic_data->setValueText($data);
						}
						$statistic_data->insert();
					}
				}
				echo json_encode(array ('success'=>true,'message'=>'<span style="color: green;">Submitted Successfully</span>'));
			} else {
				echo json_encode(array ('success'=>false,'message'=>'<span style="color: red;">Error: Please ensure the form is filled out correctly.</span>'));
			}
		} elseif ($operation === 'del') {
			if ($user->isSuperUser() || $user->isAdminUser()) {
				Statistic::delete($_POST['id']);
			}
		}
	}
}
?>