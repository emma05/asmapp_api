<?php
include_once("Utilities.php");
include_once("UserRepository.php");

class UserController {
	public static function getUser($parameters) {
		$response = array();
		$errors = array();
		if(self::validateUser($parameters) !== TRUE) {
			$errors = self::validateUser($parameters); 
			return $errors;
		}
		$user_data = UserRepository::checkUser($parameters);
		if($user_data['session_id']) {
			$user_rights = UserRepository::getUserRights($user_data['user']);
			$response = array(
				'user_id' => $user_data['user']['id'],
				'session_id' => $user_data['session_id'],
				'user_rights' => $user_rights, 
			);
			Utilities::log($response);
		} else {
			$errors[] = "Unsuccessful login!";
			$response['errors'] = $errors;
		}
		return $response;
	} 

	public static function checkSession($session_id) {
		$result = UserRepository::checkSession($session_id);
		return $result;
	}

	public static function editUser($parameters) {
		$errors = array();
		if(self::validateUser($parameters, true) !== TRUE) {
			$errors = self::validateUser($parameters, true);
			return $errors;
		}
		$result = UserRepository::editUser($parameters);
		return $result;
	}

	public function manageUser($parameters) {
		//validate user
		$result = UserRepository::manageUser($parameters);
		return $result;
	}


	private static function validateUser($parameters, $change_pass = false) {
		$errors = array();
		if($change_pass) {
			if(!isset($parameters['user_id']) || $parameters['user_id'] == null) {
				$errors[] = "User not found!";
			}
		} else {
			if(!isset($parameters['username']) || $parameters['username'] == null) {
			$errors[] = "The username cannot be empty!";
			}
		}

		if(!isset($parameters['password']) || $parameters['password'] == null) {
			$errors[] = "The password cannot be empty!";
		}

		if(count($errors) > 0 ) {
			return $errors;
		}

		return true;
	}

	public static function getUsers() {
		$result = UserRepository::getUsers();
		return $result;
	}

	public static function getData($parameters) {
		//Utilities::log($parameters);
		if(!$parameters[0]) {
			return false;
		}
		switch($parameters[0]) {
			case "apps":
				$result = UserRepository::getApps();
				break;
			case "roles":
				$result = UserRepository::getRoles();
				break;
			default:
				$result = "";
				break;
		}
		
		return $result;
	}

	public static function addUser($parameters) {
		if(self::validateUser($parameters) !== TRUE) {
			$errors = self::validateUser($parameters, true);
			return $errors;
		}
		$result = UserRepository::addUser($parameters);
		return $result;
	}

	public static function addApp($parameters) {
		$errors = array();
		if(self::validateInput($parameters['app'], "App") !== TRUE) {
			$errors = self::validateInput($parameters['app'], "App"); 
			return $errors;
		}
		$result = UserRepository::addApp($parameters);
		return $result;
	}

	public static function addRole($parameters) {
		$errors = array();
		if(self::validateInput($parameters['role'], "Role") !== TRUE) {
			$errors = self::validateInput($parameters['role'], "Role"); 
			return $errors;
		}
		if(self::validateInput($parameters['app_id'], "App") !== TRUE) {
			$errors = self::validateInput($parameters['app_id'], "App"); 
			return $errors;
		}
		$result = UserRepository::addRole($parameters);
		return $result;
	}

	private static function validateInput($parameter, $name) {
		$errors = array();

		if(!isset($parameter) || !$parameter) {
			$errors[] = $name . " field must not be empty!";
		}

		if(count($errors) > 0) {
			return $errors;
		}
		return true;
	}
}