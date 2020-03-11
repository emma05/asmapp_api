<?php
include_once("Utilities.php");
include_once("Repository.php");

class Controller {

	public static function login($parameters) {
		$response = array();
		$errors = array();
		$fields_array = array('username', 'password');
		if(self::validateInputs($parameters, $fields_array) !== TRUE) {
			$errors = self::validateInputs($parameters, $fields_array); 
			return $errors;
		}
		$user_data = Repository::checkUser($parameters);
		if($user_data['session_id']) {
			$user_rights = Repository::getUserRights($user_data['user']);
			$response = array(
				'session_id' => $user_data['session_id'],
				'user_rights' => $user_rights, 
			);
		} else {
			$errors[] = "Unsuccessful login!";
			$response['errors'] = $errors;
			return $response;
		}
		return $response;
	} 

	public static function validateSession($parameters) {
		$result = Repository::checkSession($parameters);
		return $result;
	}

	public static function checkApiToken($headers) {
		if(!isset($headers['Authorization']) ){
			return false;
		}
	    $auth = $headers['Authorization'];
		$start  = strpos($auth, '<');
		$end    = strpos($auth, '>', $start + 1);
		$length = $end - $start;
		$token = substr($auth, $start + 1, $length - 1);
		$api_token = Utilities::getParams('api_token');
	    if($token === $api_token) {
	    	return true;
	    }
	    return false;
	}

	public static function editUser($parameters) {
		$fields_array = array('password', 'user_id');
		if(isset($parameters['session_id'])) {
			$fields_array = array('password', 'session_id');
		}
		if(self::validateInputs($parameters, $fields_array) !== TRUE) {
			return self::validateInputs($parameters, $fields_array); 
		}
		$result = Repository::editUser($parameters);
		return $result;
	}

	public function manageUser($parameters) { 
		$fields_array = array('user_id', 'user_rights_access');
		if(self::validateInputs($parameters, $fields_array) !== TRUE) {
			return self::validateInputs($parameters, $fields_array); 
		}
		$result = Repository::manageUser($parameters);
		return $result;
	}

	public static function getUsers() {
		$result = Repository::getUsers();
		return $result;
	}

	public static function getData($parameters) {
		if(!$parameters['type']) {
			return false;
		}
		switch($parameters['type']) {
			case "apps":
				$result = Repository::getApps();
				break;
			case "roles":
				$result = Repository::getRoles();
				break;
			case "roles_app":
				$app = true;
				$result = Repository::getRoles($app);
			default:
				$result = "";
				break;
		}
		
		return $result;
	}

	public static function addUser($parameters) {
		$fields_array = array('username', 'password', 'app', 'role');
		if(self::validateInputs($parameters, $fields_array) !== TRUE) {
			return self::validateInputs($parameters, $fields_array);
		}
		$result = Repository::addUser($parameters);
		return $result;
	}

	public static function addApp($parameters) {
		$fields_array = array('app');
		$errors = array();
		if(self::validateInputs($parameters, $fields_array) !== TRUE) {
			return self::validateInputs($parameters, $fields_array); 
		}
		$result = Repository::addApp($parameters);
		return $result;
	}

	public static function addRole($parameters) {
		$fields_array = array('role', 'app_id');
		if(self::validateInputs($parameters, $fields_array) !== TRUE) {
			return self::validateInputs($parameters, $fields_array);
		}
		$result = Repository::addRole($parameters);
		return $result;
	}

	public static function addAccessRight($parameters) {
		$fields_array = array('access_right', 'role_id');
		if(self::validateInputs($parameters, $fields_array) !== TRUE) {
			return self::validateInputs($parameters, $fields_array);
		}
		$result = Repository::addAccessRight($parameters);
		return $result;
	}

	private static function validateInputs($parameters, $fields_array) {
		$errors = array();

		if(!$parameters || !$fields_array) {
			return false;
		}
		foreach($fields_array as $field) {
			if(!isset($parameters[$field]) || !$parameters[$field]) {
				$errors[] = "Field must not be empty";
			}
		}
		if(count($errors) > 0) {
			return $errors;
		}
		return true;
	}

}