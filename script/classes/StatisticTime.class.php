<?php
require_once('Base.class.php');

// Queries used by the StatisticTime class.
define('SQL_STATISTICTIME_FETCH', 'SELECT * FROM statistic_time WHERE time_id = ? LIMIT 1');
define('SQL_STATISTICTIME_INSERT', 'INSERT INTO statistic_time (statistic_id, attendees, start_time, end_time) VALUES(?, ?, ?, ?)');
define('SQL_STATISTICTIME_UPDATE', 'UPDATE statistic_time SET statistic_id = ?, attendees = ?, start_time = ?, end_time = ? WHERE time_id = ?');
define('SQL_STATISTICTIME_DELETE', 'DELETE FROM statistic_time WHERE time_id = ?');
define('SQL_STATISTICTIME_LIST', 'SELECT * FROM statistic_time WHERE statistic_id = ?');

// The StatisticTime class extends Base (incorporating the required abstract functionality), and
// is used to query and manipulate the records contained in the database.

class StatisticTime extends Base {
	private $_timeID;
	private $_statisticID;
	private $_attendees;
	private $_startTime;
	private $_endTime;
	
	// The main contructor for the StatisticTime class, directly corresponding to the 
	// structure defined in the relating database table.
	public function StatisticTime($timeID, $statisticID, $attendees, $startTime, $endTime) {
		$this->setTimeID($timeID);
		$this->setStatisticID($statisticID);
		$this->setAttendees($attendees);
		$this->setStartTime($startTime);
		$this->setEndTime($endTime);
	}
	
	// Getters and setters for the class
	public function getTimeID() {
		return $this->_timeID;
	}
	
	public function setTimeID($timeID) {
		$this->_timeID = $timeID;
	}
	
	public function getStatisticID() {
		return $this->_statisticID;
	}
	
	public function setStatisticID($statisticID) {
		$this->_statisticID = $statisticID;
	}
	
	public function getAttendees() {
		return $this->_attendees;
	}
	
	public function setAttendees($attendees) {
		$this->_attendees = $attendees;
	}
	
	public function getStartTime() {
		return $this->_startTime;
	}
	
	public function getEndTime() {
		return $this->_endTime;
	}
	
	public function setStartTime($startTime) {
		$this->_startTime = $startTime;
	}
	
	public function setEndTime($endTime) {
		$this->_endTime = $endTime;
	}
	
	// Used to insert a new record based on the instance data.
	public function insert() {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICTIME_INSERT);
		$statement->bind_param('iiss', $this->getStatisticID(),
							$this->getAttendees(),
							$this->getStartTime(),
							$this->getEndTime());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// The update method will update the corresponding record stored in the database.	
	public function update() {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICTIME_UPDATE);
		$statement->bind_param('iissi', $this->getStatisticID(),
							$this->getAttendees(),
							$this->getStartTime(),
							$this->getEndTime(),
							$this->getTimeID());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// The toArray method converts the instance data into an array format, which will
	// be returned to the client in a converted JSON format.
	public function toArray() {
		$data = array();
		$data[0] = $this->getTimeID();
		$data[1] = $this->getAttendees();
		$data[2] = $this->getStartTime();
		$data[3] = $this->getEndTime();
		return $data;
	}
	
	// The fetchList method provides a complete listing of the StatisticTime objects, querying the stored data
	// in the table, and instantiating a StatisticTime object for each record.
	public static function fetchList($statisticID) {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICTIME_LIST);
		$times = array();
		$statement->bind_param("i", $statisticID);
		$statement->execute();
		$statement->bind_result($timeID, $statisticID, $attendees, $startTime, $endTime);
		while($statement->fetch()) {
			$times[] = new StatisticTime($timeID, $statisticID, $attendees, $startTime, $endTime);
		}
		$db->close();
		return $times;
	}
	
	// The create method acts as a factory, which receives the timeID, and fetches the corresponding record
	// from the database; instantiating a new StatisticTime object with the received data.
	public static function create($timeID) {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICTIME_FETCH);
		$statement->bind_param("i", $timeID);
		$statement->execute();
		$statement->bind_result($timeID, $statisticID, $attendees, $startTime, $endTime);
		$statement->fetch();
		$db->close();
		return new StatisticTime($timeID, $statisticID, $attendees, $startTime, $endTime);
	}
	
	// The delete method is a static method, which enables the ability to delete a StatisticTime record based on the 
	// received timeID.
	public static function delete($timeID) {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICTIME_DELETE);
		$statement->bind_param("i", $timeID);
		$statement->execute();
		$db->close();
		return true;
	}
}
?>