<?php
// Connection information
define('DB_HOST_NAME', 'localhost');
define('DB_USER_NAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'library_system');

// The DB class provides the basic functionality for connecting to a MySQL database. It extends 
// the MySQLi class, acting as a simple wrapper which contains the connection information; enabling
// the ability to instantiate a DB object without having to specify the connection details each time.
class DB extends mysqli  {
	public function DB() {
		// Invokes the super contructor passing the declared constant values.
		parent::__construct(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD, DB_NAME);
	}
}
?>