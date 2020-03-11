<?php

include_once("Controller.php");
include_once("Utilities.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$parameters = $_POST;
} else {
	$parameters = $_GET;
}

$headers = apache_request_headers();
$auth = Controller::checkApiToken($headers);
if($auth) {
	if(isset($uri[1])) {
		switch($uri[1]) {
			case "login" :
				$result = Controller::login($parameters);
				break;
			case "validate_session" :
				$result = Controller::validateSession($parameters);
				break;
			case "edit_user":
				$result = Controller::editUser($parameters);
				break;
			case "get_users":
				$result = Controller::getUsers();
				break;
			case "manage_user":
				$result = Controller::manageUser($parameters);
				break;
			case "get":
				$result = Controller::getData($parameters);
				break;
			case "add_user":
				$result = Controller::addUser($parameters);
				break;
			case "add_app":
				$result = Controller::addApp($parameters);
				break;
			case "add_role":
				$result = Controller::addRole($parameters);
				break;
			case "add_access_right":
				$result = Controller::addAccessRight($parameters);
				break;	

			default:
			 	$result = array('errors' => "Invalid API Request");
			 	break;
		}
	} else {
		$result = array('errors' => "Invalid API Request");
	}
} else {
	$result = array("errors" => "Unauthorized request!");
}

echo json_encode($result);

?>