<?php
require_once('DB.class.php');
// The base class provides abstract methods which are required for all objects interacting with the 
// database; ensuring that the extending classes implement core functionality for inserting, and updating
// records. The abstract toArray() method is required by objects to covert the readable output data into
// a format that can be easily converted to a JSON object (the JSON being data that will be returned back
// to the client javascript).
abstract class Base {
	
	// Required method to insert a new record.
	abstract public function insert();
	// Required method to update the existing record.
	abstract public function update();
	// Required method to convert the data into a readable array format.
	abstract public function toArray();
}
?>