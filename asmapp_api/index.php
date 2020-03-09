<?php

include_once("API_Request.php");
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

if(isset($uri[1])) {
	switch($uri[1]) {
		case "login" :
			//"PHP_AUTH_USER":"emma.rus93@gmail.com","PHP_AUTH_PW":"aaa"
			$parameters['username'] = $_SERVER['PHP_AUTH_USER'];
			$parameters['password'] = $_SERVER['PHP_AUTH_PW'];
			$result = API_Request::login($parameters);
			break;
		case "validate_session" :
			$result = API_Request::validateSession($parameters);
			break;
		case "edit_user" :
			$result = API_Request::editUser($parameters);
			break;
		case "get_users":
			$result = API_Request::getUsers();
			break;
		case "manage_user":
			$result = API_Request::manageUser($parameters);
			break;
		case "get":
			$result = API_Request::getData($parameters);
			break;
		case "add_user":
			$result = API_Request::addUser($parameters);
			break;
		case "add_app":
			$result = API_Request::addApp($parameters);
			break;
		case "add_role":
			$result = API_Request::addRole($parameters);
			break;
		default:
		 	$result = array('errors' => "Invalid API Request");
		 	break;
	}
	echo json_encode($result);
}

?>