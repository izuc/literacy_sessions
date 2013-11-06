<?php
require_once('DB.class.php');
require_once('Base.class.php');
require_once('User.class.php');
define('SQL_STATISTIC_GRAPH', 'SELECT s.date_occurred, COUNT(t.time_id), SUM(t.attendees) 
					FROM statistic s INNER JOIN statistic_time t ON t.statistic_id = s.statistic_id
					WHERE s.library_id = ? AND (s.date_occurred BETWEEN ? AND ?) GROUP BY s.date_occurred');
					
// The Graph Data class is used as a container object for the the tallied statistic records. The class contains one
// main method called generate, which will query the statistical data totalling the amount of attendees and training sessions
// that ocurred on each day. 

class GraphData {
	private $_dateOccurred;
	private $_amountSessions;
	private $_amountStudents;
	
	// The constructor receives the date, and the tallied data.
	public function GraphData($dateOccurred, $amountSessions, $amountStudents) {
		$this->setDateOccurred($dateOccurred);
		$this->setAmountSessions($amountSessions);
		$this->setAmountStudents($amountStudents);
	}
	
	// Getter for the date occurred.
	public function getDateOccurred() {
		return $this->_dateOccurred;
	}
	
	// Setter for the date occurred.
	public function setDateOccurred($dateOccurred) {
		$this->_dateOccurred = $dateOccurred;
	}
	
	// Getter for the amount of sessions.
	public function getAmountSessions() {
		return $this->_amountSessions;
	}
	
	// Setter for the amount of sessions.
	public function setAmountSessions($amountSessions) {
		$this->_amountSessions = $amountSessions;
	}
	
	// Getter for the amount of students.
	public function getAmountStudents() {
		return $this->_amountStudents;
	}
	
	// Setter for the amount of students.
	public function setAmountStudents($amountStudents) {
		$this->_amountStudents = $amountStudents;
	}
	
	// Converts the instance data to an array.
	public function toArray() {
		$data = array();
		$data[0] = $this->getDateOccurred();
		$data[1] = $this->getAmountSessions();
		$data[2] = $this->getAmountStudents();
		return $data;
	}
	
	// Generates an object array of graph data, which is queried based on the received date range.
	public static function generate($start_date, $end_date) {
		$db = new DB(); // Connects to the Database.
		$statement = $db->prepare(SQL_STATISTIC_GRAPH); // Prepares the query statement.
		// Binds the received parameters to the query statement, and will only query the data based 
		// on the logged in users LibraryID.
		$statement->bind_param("iss", User::getLoggedInUser()->getLibraryID(), $start_date, $end_date);
		$statement->execute(); // Executes the query.
		// Binds the result from the query.
		$statement->bind_result($date, $sessions, $students);
		$array = array(); // Creates an array to contain the objects instantiated.
		while($statement->fetch()) { // iterates for every record returned from the query.
			$array[] = new GraphData($date, $sessions, $students); // Instantiates a Graph Data instance adding it to the object array.
		}
		$db->close(); // Closes the connection.
		return $array; // Returns the object array.
	}
}?>