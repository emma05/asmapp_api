<?php
include_once("UserController.php");

class API_Request {

	public static  function login($parameters) {
		$result = UserController::getUser($parameters);
		return $result;
	} 

	public static function validateSession($parameters){
			$session_id = $parameters['session_id'];

			$session_id = UserController::checkSession($session_id);
			if(!$session_id) {
				return false;
			} 

			return $session_id;	
	}

	public static function editUser($parameters) {
		$result = UserController::editUser($parameters);
		return $result;
	}
	public static function getUsers() {
		$result = UserController::getUsers();
		return $result;
	}
	public static function manageUser($parameters) {
		$result = UserController::manageUser($parameters);
		return $result;
	}
	public static function getData($parameters) {
		$result = UserController::getData($parameters);
		return $result;
	}

	public static function addUser($parameters) {
		$result = UserController::addUser($parameters);
		return $result;
	}
	public static function addApp($parameters) {
		$result = UserController::addApp($parameters);
		return $result;
	}
	public static function addRole($parameters) {
		$result = UserController::addRole($parameters);
		return $result;
	}

}
