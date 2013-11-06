<?php
require_once('Base.class.php');

// Queries used by the Statistic class.
define('SQL_STATISTIC_FETCH', 'SELECT * FROM statistic WHERE statistic_id = ? LIMIT 1');
define('SQL_STATISTIC_INSERT', 'INSERT INTO statistic (library_id, session_id, user_id, date_occurred) VALUES(?, ?, ?, ?)');
define('SQL_STATISTIC_UPDATE', 'UPDATE statistic SET session_id = ?, date_occurred = ? WHERE statistic_id = ?');
define('SQL_STATISTIC_DELETE', 'DELETE FROM statistic WHERE statistic_id = ?');
define('SQL_STATISTIC_LIST', 'SELECT * FROM statistic WHERE library_id = ?');

// The Statistic class extends Base (incorporating the required abstract functionality), and
// is used to query and manipulate the records contained in the database.

class Statistic extends Base {
	private $_statisticID;
	private $_libraryID;
	private $_sessionID;
	private $_userID;
	private $_dateLodged;
	private $_dateOccurred;
	
	// The main contructor for the Statistic class, directly corresponding to the 
	// structure defined in the relating database table.
	public function Statistic($statisticID, $libraryID, $sessionID, $userID, $dateLodged, $dateOccurred) {
		$this->setStatisticID($statisticID);
		$this->setLibraryID($libraryID);
		$this->setSessionID($sessionID);
		$this->setUserID($userID);
		$this->setDateLodged($dateLodged);
		$this->setDateOccurred($dateOccurred);
	}
	
	// Getter for the Statistic ID
	public function getStatisticID() {
		return $this->_statisticID;
	}
	
	// Setter for the Statistic ID
	public function setStatisticID($statisticID) {
		$this->_statisticID = $statisticID;
	}
	
	// Getter for the Library ID
	public function getLibraryID() {
		return $this->_libraryID;
	}
	
	// Setter for the Library ID
	public function setLibraryID($libraryID) {
		$this->_libraryID = $libraryID;
	}
	
	// Getter for the Session ID
	public function getSessionID() {
		return $this->_sessionID;
	}
	
	// Setter for the Session ID
	public function setSessionID($sessionID) {
		$this->_sessionID = $sessionID;
	}
	
	// Getter for the User ID
	public function getUserID() {
		return $this->_userID;
	}
	
	// Setter for the Session ID
	public function setUserID($userID) {
		$this->_userID = $userID;
	}
	
	// Getter for the Date Lodged
	public function getDateLodged() {
		return $this->_dateLodged;
	}
	
	// Setter for the Date Lodged
	public function setDateLodged($dateLodged) {
		$this->_dateLodged = $dateLodged;
	}
	
	// Getter for the Date Occurred
	public function getDateOccurred() {
		return $this->_dateOccurred;
	}
	
	// Setter for the Date Occurred
	public function setDateOccurred($dateOccurred) {
		$this->_dateOccurred = $dateOccurred;
	}
	
	// Used to insert a new record based on the instance data.
	public function insert() {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTIC_INSERT);
		$statement->bind_param('iiis', $this->getLibraryID(), $this->getSessionID(), $this->getUserID(), $this->getDateOccurred());
		$statement->execute();
		return $db->insert_id;
	}
	
	// The update method will update the corresponding record stored in the database.
	public function update(){
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTIC_UPDATE);
		$statement->bind_param('is', $this->getSessionID(), $this->getDateOccurred());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// The toArray method converts the instance data into an array format, which will
	// be returned to the client in a converted JSON format.
	public function toArray() {
		$data = array();
		$data[0] = $this->getStatisticID();
		$data[1] = $this->getLibraryID();
		$data[2] = $this->getSessionID();
		$data[3] = $this->getUserID();
		$data[4] = $this->getDateLodged();
		$data[5] = $this->getDateOccurred();
		return $data;
	}
	
	// The fetchList method provides a complete listing of the Statistic objects, querying the stored data
	// in the table, and instantiating a Statistic object for each record.
	public static function fetchList($libraryID) {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTIC_LIST);
		$statement->bind_param("i", $libraryID);
		$data = array();
		$statement->execute();
		$statement->bind_result($statisticID, $libraryID, $sessionID, $userID, $dateLodged, $dateOccurred);
		while($statement->fetch()) {
			$data[] = new Statistic($statisticID, $libraryID, $sessionID, $userID, $dateLodged, $dateOccurred);
		}
		$db->close();
		return $data;
	}
	
	// The create method acts as a factory, which receives the statisticID, and fetches the corresponding record
	// from the database; instantiating a new Statistic object with the received data.
	public static function create($statisticID) {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTIC_FETCH);
		$statement->bind_param("i", $statisticID);
		$statement->execute();
		$statement->bind_result($statisticID, $libraryID, $sessionID, $userID, $dateLodged, $dateOccurred);
		$statement->fetch();
		$db->close();
		return new Statistic($statisticID, $libraryID, $sessionID, $userID, $dateLodged, $dateOccurred);
	}
	
	// The delete method is a static method, which enables the ability to delete a Statistic based on the 
	// received statisticID.	
	public static function delete($statisticID) {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTIC_DELETE);
		$statement->bind_param('i', $statisticID);
		$statement->execute();
		$db->close();
		return true;
	}	
}
?>