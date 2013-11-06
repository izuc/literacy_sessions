<?php
require_once('Base.class.php');
require_once('Field.class.php');
require_once('FieldValue.class.php');

// Queries used by the StatisticData class.
define('SQL_STATISTICDATA_FETCH', 'SELECT * FROM statistic_data WHERE data_id = ? LIMIT 1');
define('SQL_STATISTICDATA_INSERT', 'INSERT INTO statistic_data (statistic_id, field_id, value_id, value_text) VALUES(?, ?, ?, ?)');
define('SQL_STATISTICDATA_UPDATE', 'UPDATE statistic_data SET statistic_id = ?, field_id = ?, value_id = ?, value_text = ? WHERE data_id = ?');
define('SQL_STATISTICDATA_DELETE', 'DELETE FROM statistic_data WHERE data_id = ?');
define('SQL_STATISTICDATA_LIST', 'SELECT * FROM statistic_data WHERE statistic_id = ?');

// The StatisticData class extends Base (incorporating the required abstract functionality), and
// is used to query and manipulate the records contained in the database.

class StatisticData extends Base {
	private $_dataID;
	private $_statisticID;
	private $_fieldID;
	private $_valueID;
	private $_valueText;
	
	// The main contructor for the StatisticData class, directly corresponding to the 
	// structure defined in the relating database table.
	public function StatisticData($dataID, $statisticID, $fieldID, $valueID, $valueText) {
		$this->setDataID($dataID);
		$this->setStatisticID($statisticID);
		$this->setFieldID($fieldID);
		$this->setValueID($valueID);
		$this->setValueText($valueText);
	}
	
	// Getters and setters for the class
	public function getDataID() {
		return $this->_dataID;
	}
	
	public function setDataID($dataID) {
		$this->_dataID = $dataID;
	}
	
	public function getStatisticID() {
		return $this->_statisticID;
	}
	
	public function setStatisticID($statisticID) {
		$this->_statisticID = $statisticID;
	}
	
	public function getFieldID() {
		return $this->_fieldID;
	}
	
	public function setFieldID($fieldID) {
		$this->_fieldID = $fieldID;
	}
	
	public function getValueID() {
		return $this->_valueID;
	}
	
	public function setValueID($valueID) {
		$this->_valueID = $valueID;
	}
	
	public function getValueText() {
		return $this->_valueText;
	}
	
	public function setValueText($valueText) {
		$this->_valueText = $valueText;
	}
	
	// Used to insert a new record based on the instance data.
	public function insert() {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICDATA_INSERT);
		$statement->bind_param('iiis', $this->getStatisticID(),
							$this->getFieldID(),
							$this->getValueID(),
							$this->getValueText());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// The update method will update the corresponding record stored in the database.	
	public function update() {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICDATA_UPDATE);
		$statement->bind_param('iiisi', $this->getStatisticID(),
							$this->getFieldID(),
							$this->getValueID(),
							$this->getValueText(),
							$this->getDataID());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// The toArray method converts the instance data into an array format, which will
	// be returned to the client in a converted JSON format.
	public function toArray() {
		$field = Field::create($this->getFieldID());
		$value = ($field->getFieldType() == 2)? FieldValue::create($this->getValueID())->getFieldValue() : $this->getValueText();
		
		$data = array();
		$data[0] = $this->getDataID();
		$data[1] = $field->getFieldLabel();
		$data[2] = $value;
		
		return $data;
	}
	
	// The fetchList method provides a complete listing of the StatisticData objects, querying the stored data
	// in the table, and instantiating a StatisticData object for each record.
	public static function fetchList($statisticID) {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICDATA_LIST);
		$statement->bind_param("i", $statisticID);
		$data = array();
		$statement->execute();
		$statement->bind_result($dataID, $statisticID, $fieldID, $valueID, $valueText);
		while($statement->fetch()) {
			$data[] = new StatisticData($dataID, $statisticID, $fieldID, $valueID, $valueText);
		}
		$db->close();
		return $data;
	}
	
	// The create method acts as a factory, which receives the dataID, and fetches the corresponding record
	// from the database; instantiating a new StatisticData object with the received data.
	public static function create($dataID) {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICDATA_FETCH);
		$statement->bind_param("i", $dataID);
		$statement->execute();
		$statement->bind_result($dataID, $statisticID, $fieldID, $valueID, $valueText);
		$statement->fetch();
		$db->close();
		return new StatisticData($dataID, $statisticID, $fieldID, $valueID, $valueText);
	}
	
	// The delete method is a static method, which enables the ability to delete a StatisticData record based on the 
	// received dataID.
	public static function delete($dataID) {
		$db = new DB();
		$statement = $db->prepare(SQL_STATISTICDATA_DELETE);
		$statement->bind_param("i", $dataID);
		$statement->execute();
		$db->close();
		return true;
	}
}
?>