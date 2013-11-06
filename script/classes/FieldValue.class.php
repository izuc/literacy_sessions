<?php
require_once('Base.class.php');

// Queries used by the FieldValue class.
define('SQL_FIELDVALUE_FETCH', 'SELECT * FROM field_value WHERE value_id = ? LIMIT 1');
define('SQL_FIELDVALUE_INSERT', 'INSERT INTO field_value (field_id, library_id, field_value) VALUES(?, ?, ?)');
define('SQL_FIELDVALUE_UPDATE', 'UPDATE field_value SET field_id = ?, library_id = ?, field_value = ?  WHERE value_id = ?');
define('SQL_FIELDVALUE_DELETE', 'DELETE FROM field_value WHERE value_id = ?');
define('SQL_FIELDVALUE_LIST', 'SELECT * FROM field_value WHERE library_id = ? AND field_id = ?');
define('SQL_FIELDVALUE_LIST_ALL', 'SELECT * FROM field_value WHERE field_id = ?');
define('SQL_FIELDVALUE_EXISTS', 'SELECT count(value_id) FROM field_value WHERE library_id = ? AND field_id = ? LIMIT 1');

// The FieldValue class extends Base, and is used in conjunction with the Field class. It's sole responsibility is for managing 
// the field values, in which are unique to each campus library.
class FieldValue extends Base {
	private $_valueID;
	private $_fieldID;
	private $_libraryID;
	private $_fieldValue;
	
	// The main constructor for the FieldValue, directly corresponding to the 
	// structure defined in the relating database table.
	public function FieldValue($valueID, $fieldID, $libraryID, $fieldValue) {
		$this->setValueID($valueID);
		$this->setFieldID($fieldID);
		$this->setLibraryID($libraryID);
		$this->setFieldValue($fieldValue);
	}
	
	// Getter for the ValueID
	public function getValueID() {
		return $this->_valueID;
	}
	
	// Setter for the ValueID
	public function setValueID($valueID) {
		$this->_valueID = $valueID;
	}
	
	// Getter for the FieldID
	public function getFieldID() {
		return $this->_fieldID;
	}
	
	// Setter for the FieldID
	public function setFieldID($fieldID) {
		$this->_fieldID = $fieldID;
	}
	
	// Getter for the LibraryID
	public function getLibraryID() {
		return $this->_libraryID;
	}
	
	// Setter for the LibraryID
	public function setLibraryID($libraryID) {
		$this->_libraryID = $libraryID;
	}
	
	// Getter for the FieldValue
	public function getFieldValue() {
		return $this->_fieldValue;
	}
	
	// Setter for the FieldValue
	public function setFieldValue($fieldValue) {
		$this->_fieldValue = $fieldValue;
	}
	
	// The getFieldOption method displays the field option element using the instance data.
	public function getFieldOption() {
		return '<option value="'.$this->getValueID().'">'.ucwords($this->getFieldValue()).'</option>';
	}
	
	// The insert method will create a new record within the database, based on the data stored in 
	// the current object.
	public function insert() {
		$db = new DB(); // Creates a connection
		// Prepares the query
		$statement = $db->prepare(SQL_FIELDVALUE_INSERT);
		// Binds the instance data as parameters
		$statement->bind_param('iis', $this->getFieldID(),
							$this->getLibraryID(),
							$this->getFieldValue());
		// Executes the query
		$statement->execute();
		$db->close(); // Closes connection
		return true; // indicates execution
	}
	
	// The update method will update the corresponding record stored in the database, based on
	// the fieldID (which directly relates to the primary identifier on the table).
	public function update() {
		$db = new DB(); // Creates a connection
		// Prepares the query
		$statement = $db->prepare(SQL_FIELDVALUE_UPDATE);
		// Binds the instance data as parameters
		$statement->bind_param('iisi', $this->getFieldID(),
							$this->getLibraryID(),
							$this->getFieldValue(),
							$this->getValueID());
		// Executes the query
		$statement->execute(); // Closes connection
		$db->close();  // indicates execution
		return true;
	}
	
	// The toArray method converts the instance data into an array format, which will
	// be returned to the client in a converted JSON format.
	public function toArray() {
		$data = array();
		$data[0] = $this->getValueID();
		$data[1] = $this->getFieldID();
		$data[2] = $this->getLibraryID();
		$data[3] = $this->getFieldValue();
		return $data;
	}
	
	// The hasValues method receives the libraryID, and fieldID which will 
	// query the table to determine whether there are any FieldValues.
	public static function hasValues($libraryID, $fieldID) {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELDVALUE_EXISTS);
		$statement->bind_param("ii", $libraryID, $fieldID);
		$statement->execute();
		$statement->bind_result($count);
		$statement->fetch();
		$db->close();
		return ($count > 0);
	}
	
	// The displayOptions method receives the libraryID and fieldID,
	// and fetches the object list, which will invoke the getFieldOption() 
	// method on each iterated instance. The output from each invocation is
	// stored in a variable which gets appended throughout the loop, and all
	// the field values will then be returned at the end.
	public static function displayOptions($libraryID, $fieldID) {
		$code = '';
		$values = self::fetchList($libraryID, $fieldID);
		foreach($values as $value) {
			$code .= $value->getFieldOption();
		}
		return $code;
	}
	
	// The fetchFieldValues method is used by the other two methods, in order to save on
	// repeated code. The method receives the db and statement objects (which already has the
	// query defined) and executes the query; iterating through each of the records, instantiating
	// a new FieldValue object which is added to the object array. The object array is then
	// returned.
	private static function fetchFieldValues($db, $statement) {
		$values = array();
		$statement->execute();
		// Binds the result fields from the query to the following variables, which uses
		// object references to mutate the variables for each statement iteration.
		$statement->bind_result($valueID, $fieldID, $libraryID, $fieldValue);
		// Iterates for each record.
		while($statement->fetch()) {
			// Instantiates a new FieldValue using the binded variables.
			$values[] = new FieldValue($valueID, $fieldID, $libraryID, $fieldValue);
		}
		$db->close();	// closes the connection.
		return $values; // returns the object array.
	}
	
	// The fetchList method returns an object array of FieldValues, for all
	// records relating to the received libraryID and fieldID.
	public static function fetchList($libraryID, $fieldID) {
		$db = new DB(); // Creates a DB connection.
		// Prepares the query statement
		$statement = $db->prepare(SQL_FIELDVALUE_LIST);
		// Binds the parameters to the statement.
		$statement->bind_param("ii", $libraryID, $fieldID);
		// Invokes the fetchFieldValues method, returning the object array.
		return self::fetchFieldValues($db, $statement);
	}
	
	// The fetchAll method retrieves all FieldValues relating to the fieldID.
	public static function fetchAll($fieldID) {
		$db = new DB(); // Creates a DB connection.
		// Prepares the statement.
		$statement = $db->prepare(SQL_FIELDVALUE_LIST_ALL);
		// Binds the fieldID parameter to the statement.
		$statement->bind_param("i", $fieldID);
		// Invokes the fetchFieldValues method, returning the object array.
		return self::fetchFieldValues($db, $statement);
	}
	
	// The create method acts as a factory, which will instantiate the object
	// relating to the valueID received.
	public static function create($valueID) {
		$db = new DB(); // Creates a DB connection.
		// Prepares the statement.
		$statement = $db->prepare(SQL_FIELDVALUE_FETCH);
		// Binds the valueID as a parameter to the prepared statement.
		$statement->bind_param("i", $valueID);
		$statement->execute(); // Executes the query.
		// Binds the result to variables.
		$statement->bind_result($valueID, $fieldID, $libraryID, $fieldValue);
		// Fetches the first record
		$statement->fetch();
		$db->close(); // Closes the connection
		// Returns an instantiated FieldValue based on the returned data.
		return new FieldValue($valueID, $fieldID, $libraryID, $fieldValue);
	}
	
	// The delete method is a static method, which enables the ability to delete a field value based on the 
	// received valueID.
	public static function delete($valueID) {
		$db = new DB(); // Creates the DB connection.
		// Prepares the statement.
		$statement = $db->prepare(SQL_FIELDVALUE_DELETE);
		// Adds the valueID as a parameter to the prepared statement.
		$statement->bind_param('i', $valueID);
		// Executes the query.
		$statement->execute();
		$db->close(); // Closes the connection
		return true; // returns a boolean true to indicate the query has been executed.
	}
}