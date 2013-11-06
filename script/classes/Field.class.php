<?php
require_once('Base.class.php');
require_once('FieldValue.class.php');

// Queries used by the Field class.
define('SQL_FIELD_FETCH', 'SELECT * FROM field WHERE field_id = ? LIMIT 1');
define('SQL_FIELD_INSERT', 'INSERT INTO field (field_label, field_type, field_required) VALUES(?, ?, ?)');
define('SQL_FIELD_UPDATE', 'UPDATE field SET field_label = ?, field_type = ?, field_required = ? WHERE field_id = ?');
define('SQL_FIELD_DELETE', 'DELETE FROM field WHERE field_id = ?');
define('SQL_FIELD_AVAILABLE_LIST', 'SELECT f.field_id, f.field_label, f.field_type, f.field_required FROM field f INNER JOIN field_available fa ON fa.field_id = f.field_id AND fa.library_id = ? AND fa.session_id = ?');
define('SQL_FIELD_LIST', 'SELECT * FROM field ORDER BY field_type');

define('FIELD_TYPE_TEXT', 1);
define('FIELD_TYPE_SELECT', 2);
define('FIELD_TYPE_TEXTAREA', 3);
define('FIELD_TYPE_DATE', 4);

// The field class provides the functionality to create field elements dynamically. It partners with two other classes called FieldValues and FieldData. The
// FieldValues class is used in the occurrence that the field element type is a dropdown/selectbox, being that the FieldValue is the values appearing once the 
// selectbox is pressed. The FieldData class is used to store the actual data submitted by the form that corresponds to each field. 

class Field extends Base {
	// The field types.
	private static $fieldTypes = array(FIELD_TYPE_TEXT => 'textbox', FIELD_TYPE_SELECT => 'selectbox', FIELD_TYPE_TEXTAREA => 'textarea', FIELD_TYPE_DATE => 'datepicker');
	private $_fieldID;
	private $_fieldLabel;
	private $_fieldType;
	private $_fieldRequired;
	
	// The field constructor used for instantiating a field object. It receives the field id, field label, field type,
	// and a indication whether the field is mandatory.
	public function Field($fieldID, $fieldLabel, $fieldType, $fieldRequired) {
		$this->setFieldID($fieldID);
		$this->setFieldLabel($fieldLabel);
		$this->setFieldType($fieldType);
		$this->setFieldRequired($fieldRequired);
	}
	
	// Getter for the field id.
	public function getFieldID() {
		return $this->_fieldID;
	}
	
	// Setter for the field id.
	public function setFieldID($fieldID) {
		$this->_fieldID = $fieldID;
	}
	
	// Getter for the field label.
	public function getFieldLabel() {
		return $this->_fieldLabel;
	}
	
	// Setter for the field label.
	public function setFieldLabel($fieldLabel) {
		$this->_fieldLabel = $fieldLabel;
	}
	
	// Setter for the field required (boolean mandatory indication).
	public function setFieldRequired($fieldRequired) {
		$this->_fieldRequired = $fieldRequired;
	}
	
	// Getter for the field required.
	public function isRequired() {
		return $this->_fieldRequired;
	}
	
	// Getter for the field type.
	public function getFieldType() {
		return $this->_fieldType;
	}
	
	// Setter for the field type.	
	public function setFieldType($fieldType) {
		$this->_fieldType = $fieldType;
	}
	
	// Private method constructing the appropriate format for the validation criteria.
	private function validationCriteria($length) {
		return 'validate[' .(($this->isRequired())? 'required' : '').(($length > 0 && $this->isRequired()) ? ',' : '') 
			.(($length > 0) ? 'length[0,' .$length. ']]' : ''). ']';
	}
	
	// Private method used for creating a string that consists of the id and name attributes.
	private function identifier() {
		$type = self::$fieldTypes[$this->getFieldType()];
		return 'id="' .$type. '_' .$this->getFieldID(). '" name="' .$type. '_' .$this->getFieldID(). '"';
	}
	
	// The showFormField method outputs the appropriate html field element based on the content stored within the object instance. The
	// field type (an instance value) is used in a switch, and for each of the possible field types an html output element is generated 
	// (using the object data) which is then outputted by echo to be displayed onto the form. The method receives the libraryID, (being the
	// library that the current logged in user is a member of) which is used by the invoked FieldValue methods enabling it to query the values
	// assigned to the library for selectbox fields (due to libraries having different FieldValues).
	public function showFormField($libraryID) {
		// Used to test the field type against the following cases.
		switch($this->getFieldType()) {
			// In the case that the fieldtype is a textbox.
			case FIELD_TYPE_TEXT:
				// It will echo the formatted html element with the appropriate identifier and validation criteria.
				echo '<input type="text" '.$this->identifier().' size="25" class="'.$this->validationCriteria(25).' text-input" />';
				break;
			// In the case that the fieldtype is a selectbox.
			case FIELD_TYPE_SELECT:
				// It will contruct and display the selectbox html element starting tag.
				echo '<select '.$this->identifier().' width="180px" style="width: 180px; height: 20px;" class="'.$this->validationCriteria(0).'">';
				// Place a default option value stating to "Please Select" a value.
				echo '<option value="">Please Select</option>';
				// It will then check whether their are FieldValues defined for the Library & Field.
				if (FieldValue::hasValues($libraryID, $this->getFieldID())) {
					// If there are field values it will then display them in the appropriate format.
					echo FieldValue::displayOptions($libraryID, $this->getFieldID());
				}
				// Closes the select tag, completing the HTML element.
				echo '</select>';
				break;
			// In the case that the fieldtype is a textarea.
			case FIELD_TYPE_TEXTAREA:
				// It will then construct the textarea html element.
				echo '<textarea '.$this->identifier().' rows="5" cols="50" class="'.$this->validationCriteria(150).' text-input"></textarea>';
				break;
			// In the case that the fieldtype is a datepicker.
			case FIELD_TYPE_DATE:
				// It will then construct a normal textbox element, with the clause datepicker defined in the class, which will
				// enable JQuery to convert it into a datepicker.
				echo '<input '.$this->identifier().' type="text" size="25" class="datepicker '.$this->validationCriteria(0).'">';
				break;
		}
	}
	
	// The showFormLabel method displays the field label in the html label format, and also marks with a asterix if the field is mandatory.
	public function showFormLabel() {
		echo '<label for="'.self::$fieldTypes[$this->getFieldType()]. '_' .$this->getFieldID().'">'.$this->getFieldLabel().' '.(($this->isRequired())?'<em>*</em>':'').'</label>';
	}
	
	// The insert method is used to insert the current field data into the Field table as a new record.
	public function insert() {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELD_INSERT);
		$statement->bind_param('sii', $this->getFieldLabel(),
							$this->getFieldType(),
							$this->isRequired());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// The update method updates the field data record located in the Field table (based on the object's field id)
	// with the data contained in this object.
	public function update() {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELD_UPDATE);
		$statement->bind_param('siii', $this->getFieldLabel(),
							$this->getFieldType(),
							$this->isRequired(),
							$this->getFieldID());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// The toArray method enables for the Field instance data to be converted to an array
	// which is then used to output as JSON to the client.
	public function toArray() {
		$data = array();
		$data[0] = $this->getFieldID();
		$data[1] = $this->getFieldLabel();
		$data[2] = $this->getFieldType();
		$data[3] = $this->isRequired();
		return $data;
	}
	
	// The fetchFieldsAvailable method is used to query for the avaliable fields which 
	// are assigned to library & session. This therefore enables the ability to have unique
	// sets of fields for each library, and each library session. The fields however are set
	// globally for all libraries, facilitating the capability to reuse fields for multiple 
	// libraries and sessions, with the fields available only restraining the display (acting as
	// permissions) for the fields.
	public static function fetchFieldsAvailable($libraryID, $sessionID) {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELD_AVAILABLE_LIST);
		$statement->bind_param("ii", $libraryID, $sessionID);
		$fields = array();
		$statement->execute();
		$statement->bind_result($fieldID, $fieldLabel, $fieldType, $fieldRequired);
		while($statement->fetch()) {
			$fields[] = new Field($fieldID, $fieldLabel, $fieldType, $fieldRequired);
		}
		$db->close();
		return $fields;
	}
	
	// The fetchList method provides a complete listing of the field objects, querying the stored data
	// in the table, and instantiating a field object for each record.
	public static function fetchList() {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELD_LIST);
		$fields = array();
		$statement->execute();
		$statement->bind_result($fieldID, $fieldLabel, $fieldType, $fieldRequired);
		while($statement->fetch()) {
			$fields[] = new Field($fieldID, $fieldLabel, $fieldType, $fieldRequired);
		}
		$db->close();
		return $fields;
	}
	
	// The create method acts as a factory, which receives the fieldID, and fetches the corresponding record
	// from the database; instantiating a new field object with the received data.
	public static function create($fieldID) {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELD_FETCH);
		$statement->bind_param("i", $fieldID);
		$statement->execute();
		$statement->bind_result($fieldID, $fieldLabel, $fieldType, $fieldRequired);
		$statement->fetch();
		$db->close();
		return new Field($fieldID, $fieldLabel, $fieldType, $fieldRequired);
	}
	
	// The delete method is a static method, which enables the ability to delete a field based on the 
	// received fieldID.
	public static function delete($fieldID) {
		$db = new DB();
		$statement = $db->prepare(SQL_FIELD_DELETE);
		$statement->bind_param("i", $fieldID);
		$statement->execute();
		$db->close();
		return true;
	}
}
?>