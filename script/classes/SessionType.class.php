<?php
require_once('Base.class.php');

// Queries used by the SessionType class.
define('SQL_SESSION_FETCH', 'SELECT * FROM session_type WHERE session_id = ? LIMIT 1');
define('SQL_SESSION_INSERT', 'INSERT INTO session_type (session_name) VALUES(?)');
define('SQL_SESSION_UPDATE', 'UPDATE session_type SET session_name = ? WHERE session_id = ?');
define('SQL_SESSION_LIST', 'SELECT * FROM session_type ORDER BY session_name ASC');
define('SQL_SESSION_DELETE', 'DELETE FROM session_type WHERE session_id = ?');

// The SessionType class extends Base (incorporating the required abstract functionality), and
// is used to query and manipulate the records contained in the database.

class SessionType extends Base {
	private $_sessionID;
	private $_sessionName;
	
	// Contructor for the SessionType, receiving the Session ID and Session Name
	public function SessionType($sessionID, $sessionName) {
		$this->setSessionID($sessionID);
		$this->setSessionName($sessionName);
	}
	
	// Getter for the Session ID
	public function getSessionID() {
		return $this->_sessionID;
	}
	
	// Setter for the Session ID
	public function setSessionID($sessionID) {
		$this->_sessionID = $sessionID;
	}
	
	// Getter for the Session Name
	public function getSessionName() {
		return $this->_sessionName;
	}
	
	// Setter for the Session Name
	public function setSessionName($sessionName) {
		$this->_sessionName = $sessionName;
	}
	
	// Used to insert a new record based on the instance data.
	public function insert() {
		$db = new DB(); // Creates connection
		$statement = $db->prepare(SQL_SESSION_INSERT); // Prepares the query
		// Binds the instance data as parameters to the query statement
		$statement->bind_param('s', $this->getSessionName());
		$statement->execute(); // Executes the query
		$db->close(); // Closes connection
		return true; // Indicates Execution
	}
	
	// The update method will update the corresponding record stored in the database.
	public function update() {
		$db = new DB(); // Creates connection
		$statement = $db->prepare(SQL_SESSION_UPDATE); // Prepares the query
		// Binds the instance data as parameters to the query statement
		$statement->bind_param('si', $this->getSessionName(),
							$this->getSessionID());
		$statement->execute(); // Executes the query
		$db->close(); // Closes connection
		return true; // Indicates Execution
	}
	
	// The toArray method converts the instance data into an array format, which will
	// be returned to the client in a converted JSON format.
	public function toArray() {
		$data = array();
		$data[0] = $this->getSessionID();
		$data[1] = $this->getSessionName();
		return $data;
	}
	
	// The fetchList method provides a complete listing of the SessionType objects, querying the stored data
	// in the table, and instantiating a SessionType object for each record.
	public static function fetchList() {
		$db = new DB(); // Creates connection
		$statement = $db->prepare(SQL_SESSION_LIST); // Prepares the query
		$session_types = array(); // Object array
		$statement->execute(); // Executes the query
		// Binds the result fields from the query to the following variables
		$statement->bind_result($sessionID, $sessionName);
		while($statement->fetch()) {
			// Instantiates a new SessionType using the binded variables.
			$session_types[] = new SessionType($sessionID, $sessionName);
		}
		$db->close(); // Closes connection
		return $session_types; // Returns the object array
	}
	
	// The create method acts as a factory, which receives the sessionID, and fetches the corresponding record
	// from the database; instantiating a new SessionType object with the received data.	
	public static function create($sessionID) {
		$db = new DB(); // Creates connection
		$statement = $db->prepare(SQL_SESSION_FETCH); // Prepares the query
		$statement->bind_param("i", $sessionID); // Binds the sessionID to the query.
		$statement->execute(); // Executes the query
		// Binds the result fields from the query to the following variables
		$statement->bind_result($sessionID, $sessionName);
		// Fetches the first result
		$statement->fetch();
		$db->close(); // Closes connection
		// Returns an instantiated SessionType 
		return new SessionType($sessionID, $sessionName);
	}
	
	// The delete method is a static method, which enables the ability to delete a library based on the 
	// received SessionID.	
	public static function delete($sessionID) {
		$db = new DB(); // Creates connection
		$statement = $db->prepare(SQL_SESSION_DELETE); // Prepares the query
		$statement->bind_param('i', $sessionID);  // Binds the sessionID to the query.
		$statement->execute(); // Executes the query
		$db->close();  // Closes connection
		return true; // Indicates Execution
	}
}
?>