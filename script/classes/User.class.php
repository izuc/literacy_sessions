<?php
require_once('Base.class.php');

// Queries used by the User class.
define('SQL_LOGIN_CREDENTIALS', 'SELECT user_id FROM user_account WHERE staff_account = ? AND account_password = SHA1(?) LIMIT 1');
define('SQL_USER_FETCH', 'SELECT * FROM user_account WHERE user_id = ? LIMIT 1');
define('SQL_FETCH_MODULES', 'SELECT module_name, module_settings FROM module WHERE account_type = ?');
define('SQL_USER_INSERT', 'INSERT INTO user_account (staff_account, library_id, account_password, account_type) VALUES(?, ?, SHA1(?), ?)');
define('SQL_USER_UPDATE', 'UPDATE user_account SET staff_account = ?, library_id = ?, account_type = ? WHERE user_id = ?');
define('SQL_USER_CHANGE_PASSWORD', 'UPDATE user_account SET account_password = SHA1(?) WHERE user_id = ?');
define('SQL_USER_LIST', 'SELECT user_id, staff_account, library_id, account_type FROM user_account ORDER BY library_id');
define('SQL_USER_DELETE', 'DELETE FROM user_account WHERE user_id = ?');

// User account types.
define('ACCOUNT_TYPE_NORMAL', 1);
define('ACCOUNT_TYPE_SUPER', 2);
define('ACCOUNT_TYPE_ADMIN', 3);

// The User class encompasses the core functionality for user authentication, consisting of the ability to load the 
// sections based on the user account type.

class User extends Base {
	private $_userID;
	private $_userStaffAccount;
	private $_libraryID;
	private $_userAccountPassword;
	private $_userAccountType;

	// The main contructor for the User class, directly corresponding to the 
	// structure defined in the relating database table.
	public function User($userID, $userStaffAccount, $libraryID, $userAccountPassword, $userAccountType) {
		$this->setUserID($userID);
		$this->setUserStaffAccount($userStaffAccount);
		$this->setLibraryID($libraryID);
		$this->setUserAccountPassword($userAccountPassword);
		$this->setUserAccountType($userAccountType);
	}
	
	// Getters and setters for the class
	public function getUserID() {
		return $this->_userID;
	}
	
	public function setUserID($userID) {
		$this->_userID = $userID;
	}
	
	public function getUserStaffAccount() {
		return $this->_userStaffAccount;
	}
	
	public function setUserStaffAccount($userStaffAccount) {
		$this->_userStaffAccount = $userStaffAccount;
	}
	
	public function getLibraryID() {
		return $this->_libraryID;
	}
	
	public function setLibraryID($libraryID) {
		$this->_libraryID = $libraryID;
	}
	
	public function getUserAccountPassword() {
		return $this->_userAccountPassword;
	}
	
	public function setUserAccountPassword($userAccountPassword) {
		$this->_userAccountPassword = $userAccountPassword;
	}
	
	public function getUserAccountType() {
		return $this->_userAccountType;
	}
	
	public function setUserAccountType($userAccountType) {
		$this->_userAccountType = $userAccountType;
	}
	
	// Checks whether the object is a super user
	public function isSuperUser() {
		return ($this->getUserAccountType() == ACCOUNT_TYPE_SUPER);
	}
	
	// Checks whether the object is a admin user
	public function isAdminUser() {
		return ($this->getUserAccountType() == ACCOUNT_TYPE_ADMIN);
	}
	
	// Loads the control panel, invoking the loadPanelModules method for each user account type.
	public function loadMyPanel() {
		echo '<div id="menu_links">';
		switch ($this->_userAccountType) {
			// Since there are no breaks, the admin will also have the lower account types modules.
			case ACCOUNT_TYPE_ADMIN:
				self::loadPanelModules(ACCOUNT_TYPE_ADMIN);
			case ACCOUNT_TYPE_SUPER:
				self::loadPanelModules(ACCOUNT_TYPE_SUPER);
			case ACCOUNT_TYPE_NORMAL:
				self::loadPanelModules(ACCOUNT_TYPE_NORMAL);
				break;
		}
		// Javascript for logging out
		echo '	<a href="#" class="menu_link" id="menu_logout">
				<img src="template/images/menu_logout.png" class="menu_title" /></a>
				<script type="text/javascript">
					$(\'#menu_logout\').click(function () { 
						var parameters = {};
						parameters.action = \'logout\';
						process_request(\'login_actions\', parameters, login_response);
					});
				</script>
			</div>';
	}
	
	// Used to insert a new record based on the instance data.
	public function insert() {
		$db = new DB();
		$statement = $db->prepare(SQL_USER_INSERT);
		$statement->bind_param('sisi', $this->getUserStaffAccount(),
							$this->getLibraryID(),
							$this->getUserAccountPassword(),
							$this->getUserAccountType());
		$statement->execute();
		$db->close();
		return true;
	}
	
	// The update method will update the corresponding record stored in the database.	
	public function update() {
		$db = new DB();
		$statement = $db->prepare(SQL_USER_UPDATE);
		$statement->bind_param('siii', $this->getUserStaffAccount(),
							$this->getLibraryID(),
							$this->getUserAccountType(),
							$this->getUserID());
		$statement->execute();
		if (strlen($this->getUserAccountPassword()) > 0) {
			$statement = $db->prepare(SQL_USER_CHANGE_PASSWORD);
			$statement->bind_param('si', $this->getUserAccountPassword(), $this->getUserID());
			$statement->execute();
		}
		$db->close();
		return true;
	}
	
	// The toArray method converts the instance data into an array format, which will
	// be returned to the client in a converted JSON format.
	public function toArray() {
		$data = array();
		$data[0] = $this->getUserID();
		$data[1] = $this->getUserStaffAccount();
		$data[2] = $this->getLibraryID();
		$data[3] = '';
		$data[4] = $this->getUserAccountType();
		return $data;
	}
	
	// The load panel modules, will load the modules defined in the database which relate to the 
	// user account type.
	private static function loadPanelModules($userAccountType) {
		$db = new DB();
		$statement = $db->prepare(SQL_FETCH_MODULES);
		$statement->bind_param("i", $userAccountType);
		$statement->execute();
		$statement->bind_result($moduleName, $parameters);
		while($statement->fetch()) {
			echo '	<span class="menu_link" id="menu_'.$moduleName.'"><img src="template/images/menu_'.$moduleName.'.png" class="menu_title" /></span>
					<script type="text/javascript">
						$(\'#menu_'. $moduleName .'\').click(function () {
							load_content(\''. $moduleName .'.php'.((strlen($parameters) > 0)? '?'.$parameters : '').'\');
						});
					</script>';
		}
		$db->close();
	}
	
	// Displays the login panel
	public static function showLoginArea() {
		echo '
		<form id="login">
		  <fieldset id="login_menu">
			<p>
			  <label for="staff_account">Staff ID</label>
			  <input id="staff_account" class="validate[required,custom[noSpecialCaracters],length[0,20]] text-input" name="staff_account" value="" title="Staff ID" tabindex="1" type="text" />
			</p>
			<p>
			  <label for="account_password">Password</label>
			  <input id="account_password" class="validate[required,custom[noSpecialCaracters],length[0,20]] text-input" name="account_password" value="" title="Password" tabindex="2" type="password" />
			</p>
			<p class="login">
				<input value="Login" tabindex="3" type="submit" class="buttonSubmit" />
			</p>
		  </fieldset>
		</form>
		<script type="text/javascript">
		$(\'#login\').validationEngine({
			success : function() {
				var parameters = $(\'#login\').serializeForm();
				parameters.action = \'login\';
				process_request(\'login_actions\', parameters, login_response);
			},
			failure: function() {}
		});
		</script>';
	}
	
	// Validates the user credentials
	public static function login($userStaffAccount, $userAccountPassword) {
		if (self::getLoggedInUser() == null) {
			$db = new DB();
			$statement = $db->prepare(SQL_LOGIN_CREDENTIALS);
			$statement->bind_param("ss", strtoupper($userStaffAccount), $userAccountPassword);
			$statement->execute();
			$statement->bind_result($userID);
			$statement->fetch();
			$db->close();
			if ($userID > 0) {
				$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
				$_SESSION['user'] = $userID;
				return true;
			}
		}
		return false;
	}
	
	// Terminates the session
	public static function logout() {
		session_unset();  
		session_destroy();
		return true;
	}
	
	// Grabs the user id stored in the session, and will instantiate a user object 
	// based on that id value. Validates also whether it is a validate session.
	public static function getLoggedInUser() {
		if (isset($_SESSION['agent']) AND ($_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT']))) {
			if (isset($_SESSION['user'])) {
				$userID = $_SESSION['user'];
				if ($userID > 0) {
					return self::create($userID);
				}
			}
		}
		return null;
	}
	
	// Fetches the object list of users.
	public static function fetchList() {
		$db = new DB();
		$statement = $db->prepare(SQL_USER_LIST);
		$users = array();
		$statement->execute();
		$statement->bind_result($userID, $userStaffAccount, $libraryID, $userAccountType);
		while($statement->fetch()) {
			$users[] = new User($userID, $userStaffAccount, $libraryID, null, $userAccountType);
		}
		$db->close();
		return $users;
	}
	
	// Creates a user object based on the received userID
	public static function create($userID) {
		$db = new DB();
		$statement = $db->prepare(SQL_USER_FETCH);
		$statement->bind_param("i", $userID);
		$statement->execute();
		$statement->bind_result($userID, $userStaffAccount, $libraryID, $userAccountPassword, $userAccountType);
		$statement->fetch();
		$db->close();
		return new User($userID, $userStaffAccount, $libraryID, $userAccountPassword, $userAccountType);
	}
	
	// Deletes a user record based on the received userID
	public static function delete($userID) {
		$db = new DB();
		$statement = $db->prepare(SQL_USER_DELETE);
		$statement->bind_param("i", $userID);
		$statement->execute();
		$db->close();
		return true;
	}
}
?>