<?php
require_once('Base.class.php');

// Queries used by the FieldAvailable class.
define('SQL_FIELDAVAILABLE_FETCH', 'SELECT * FROM field_available WHERE value_id = ? LIMIT 1');
define('SQL_FIELDAVAILABLE_INSERT', 'INSERT INTO field_available (library_id, session_id, field_id) VALUES(?, ?, ?)');
define('SQL_FIELDAVAILABLE_UPDATE', 'UPDATE field_available SET library_id = ?, session_id = ?, field_id = ? WHERE available_id = ?');
define('SQL_FIELDAVAILABLE_DELETE', 'DELETE FROM field_available WHERE available_id = ?');
define('SQL_FIELDAVAILABLE_LIST', 'SELECT * FROM field_available WHERE field_id = ?');
define('SQL_FIELDAVAILABLE_EXISTS', 'SELECT count(available_id) FROM field_available WHERE library_id = ? AND session_id = ? LIMIT 1');

// The FieldAvailable class is used to manipulate the access restraints stored in the relating table (consisting of the ability to 
// insert new records, and update existing records), and also to query the records stored which convert the result into a structured 
// object instance. 
class FieldAvailable extends Base {
	private $_availableID;
	private $_libraryID;
	private $_sessionID;
	private $_fieldID;
	
	// The constructor used to instantiate a instance, accepting the fields which
	// correspond to the structure within the relating table.
	public function FieldAvailable($availableID, $libraryID, $sessionID, $fieldID) {
		$this->setAvailableID($availableID);
		$this->setLibraryID($libraryID);
		$this->setSessionID($sessionID);
		$this->setFieldID($fieldID);
	}
	
	// Getter for the AvailableID (Primary ID).
	public function getAvailableID() {
		return $this->_availableID;
	}
	
	// Setter for the AvilableID
	public function setAvailableID($availableID) {
		$this->_availableID = $availableID;
	}
	
	// Getter for the LibraryID
	public function getLibraryID() {
		return $this->_libraryID;
	}
	
	// Setter for the LibraryID
	public function setLibraryID($libraryID) {
		$this->_libraryID = $libraryID;
	}
	
	// Getter for the SessionID
	public function getSessionID() {
		return $this->setSessionID;
	}
	
	// Setter for the SessionID
	public function setSessionID($sessionID) {
		$this->setSessionID = $sessionID;
	}
	
	// Getter for the FieldID
	public function getFieldID() {
		return $this->_fieldID;
	}
	
	// Setter for the FieldID
	public function setFieldID($fieldID) {
		$this->_fieldID = $fieldID;
	}

	// Inserts a new record into the relating table using the instance data.
	public function insert() {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELDAVAILABLE_INSERT);
		$statement->bind_param('iii', $this->getLibraryID(),
							$this->getSessionID(),
							$this->getFieldID());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// Updates the existing record in the table (based on the availableID), replacing
	// it with the instance data.
	public function update() {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELDAVAILABLE_UPDATE);
		$statement->bind_param('iiii', $this->getLibraryID(),
							$this->getSessionID(),
							$this->getFieldID(),
							$this->getAvailableID());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// Converts the instance object data to an array.
	public function toArray() {
		$data = array();
		$data[0] = $this->getAvailableID();
		$data[1] = $this->getLibraryID();
		$data[2] = $this->getSessionID();
		$data[3] = $this->getFieldID();
		return $data;
	}
	
	// Returns an object array of all FieldAvailable retraints which correspond
	// to the received Field ID value.
	public static function fetchList($fieldID) {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELDAVAILABLE_LIST);
		$statement->bind_param("i", $fieldID);
		$values = array();
		$statement->execute();
		$statement->bind_result($availableID, $libraryID, $sessionID, $fieldID);
		while($statement->fetch()) {
			$values[] = new FieldAvailable($availableID, $libraryID, $sessionID, $fieldID);
		}
		$db->close();
		return $values;
	}
	
	// Fetches the record from the database based on the received fieldID, and returns a newly instantiated
	// instance based on the retrieved data from the query.
	public static function create($fieldID) {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELDAVAILABLE_FETCH);
		$statement->bind_param("i", $fieldID);
		$statement->execute();
		$statement->bind_result($availableID, $libraryID, $sessionID, $fieldID);
		$statement->fetch();
		$db->close();
		return new FieldAvailable($availableID, $libraryID, $sessionID, $fieldID);
	}
	
	// Deletes the FieldAvailable record in the database based on the received availableID.
	public static function delete($availableID) {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELDAVAILABLE_DELETE);
		$statement->bind_param('i', $availableID);
		$statement->execute();
		$db->close();
		return true;
	}
	
	// Checks whether the session has fields defined for the received libraryID, and sessionID.
	public static function hasFields($libraryID, $sessionID) {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELDAVAILABLE_EXISTS);
		$statement->bind_param("ii", $libraryID, $sessionID);
		$statement->execute();
		$statement->bind_result($count);
		$statement->fetch();
		$db->close();
		return ($count > 0);
	}
}