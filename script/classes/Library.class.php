<?php
require_once('Base.class.php');

// Queries used by the Library class.
define('SQL_LIBRARY_FETCH', 'SELECT * FROM library WHERE library_id = ? LIMIT 1');
define('SQL_LIBRARY_INSERT', 'INSERT INTO library (library_name) VALUES(?)');
define('SQL_LIBRARY_UPDATE', 'UPDATE library SET library_name = ? WHERE library_id = ?');
define('SQL_LIBRARY_DELETE', 'DELETE FROM library WHERE library_id = ?');
define('SQL_LIBRARY_LIST', 'SELECT * FROM library');

// The Library class extends Base (incorporating the required abstract functionality), and
// is used to query and manipulate the records contained in the database.

class Library extends Base {
	private $_libraryID;
	private $_libraryName;
	
	// The contructor for the Library object, receiving the libraryID (primarily ID) and libraryName.
	public function Library($libraryID, $libraryName) {
		$this->setLibraryID($libraryID);
		$this->setLibraryName($libraryName);
	}
	
	// Getter for the Library ID
	public function getLibraryID() {
		return $this->_libraryID;
	}
	
	// Setter for the Library ID	
	public function setLibraryID($libraryID) {
		$this->_libraryID = $libraryID;
	}
	
	// Getter for the Library Name
	public function getLibraryName() {
		return $this->_libraryName;
	}
	
	// Setter for the Library Name
	public function setLibraryName($libraryName) {
		$this->_libraryName = $libraryName;
	}
	
	// Used to insert a new record based on the instance data.
	public function insert() {
		$db = new DB(); // Creates connection
		// Prepares the query
		$statement = $db->prepare(SQL_LIBRARY_INSERT);
		// Binds the instance data as parameters to the query statement
		$statement->bind_param('s', $this->getLibraryName());
		// Executes the query
		$statement->execute();
		$db->close(); // Closes connection
		return true;
	}
	
	public function update() {
		$db = new DB(); // Creates connection
		// Prepares the query
		$statement = $db->prepare(SQL_LIBRARY_UPDATE);
		// Binds the instance data as parameters to the query statement
		$statement->bind_param('si', $this->getLibraryName(),
							$this->getLibraryID());
		// Executes the query
		$statement->execute();
		$db->close(); // Closes connection
		return true;
	}

	// The toArray method converts the instance data into an array format, which will
	// be returned to the client in a converted JSON format.
	public function toArray() {
		$data = array();
		$data[0] = $this->getLibraryID();
		$data[1] = $this->getLibraryName();
		return $data;
	}
	
	// The fetchList method provides a complete listing of the library objects, querying the stored data
	// in the table, and instantiating a library object for each record.
	public static function fetchList() {
		$db = new DB(); // Creates connection
		$statement = $db->prepare(SQL_LIBRARY_LIST); // Prepares the query
		$libraries = array(); // Object array
		$statement->execute(); // Executes the query
		// Binds the result fields from the query to the following variables
		$statement->bind_result($libraryID, $libraryName);
		while($statement->fetch()) {
			// Instantiates a new Library using the binded variables.
			$libraries[] = new Library($libraryID, $libraryName);
		}
		$db->close(); // Closes connection
		return $libraries; // Returns the object array
	}
	
	// The create method acts as a factory, which receives the libraryID, and fetches the corresponding record
	// from the database; instantiating a new library object with the received data.	
	public static function create($libraryID) {
		$db = new DB();  // Creates connection
		$statement = $db->prepare(SQL_LIBRARY_FETCH); // Prepares the query
		// Adds the libraryID as a parameter
		$statement->bind_param("i", $libraryID);
		$statement->execute(); // Executes the query
		// Binds the result fields from the query to the following variables
		$statement->bind_result($libraryID, $libraryName);
		// Fetches the record
		$statement->fetch();
		$db->close(); // Closes connection
		// Returns a new Library instantiation based on the query result.
		return new Library($libraryID, $libraryName);
	}
	
	// The delete method is a static method, which enables the ability to delete a library based on the 
	// received libraryID.	
	public static function delete($libraryID) {
		$db = new DB(); // Creates connection
		$statement = $db->prepare(SQL_LIBRARY_DELETE); // Prepares the query
		$statement->bind_param("i", $libraryID); // Adds the libraryID as a parameter
		$statement->execute(); // Executes the query
		$db->close(); // Closes connection
		return true; // Indicating Execution
	}
}