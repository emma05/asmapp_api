<?php

class Utilities {

	public static $user_type_id = array(
								'user' => 1,
								'admin' => 2,
								);

	public static function getDatabaseConnection() {
		$db_params = self::getParams('database', true);
		if(!$db_params) {
			self::log('Database parameters not found!');
			return false;
		}
		$hostname = $db_params['hostname'];
		$username = $db_params['username'];
		$password = $db_params['password'];
		$database = $db_params['database_name'];
		try {
			$conn = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //check
		} catch(PDOException $e) {
		    self::log("Database connection failed: " . $e->getMessage());
		    return false;
		}

		return $conn;
	}

	public static function getParams($name, $section = false) { 
		if(!$name) {
			return false;
		}
		$parameters = parse_ini_file('config/parameters.ini');
		if($section) {
			$parameters = parse_ini_file('config/parameters.ini', true);
		}
		if(!isset($parameters[$name])) {
			return false;
		}
		return $parameters[$name];
	}

	public static function log($error) {
		$filename = self::getParams('root_dir') . "/logs/error.log";
		$datetime = new DateTime();
		$datetime_format = $datetime->format('Y-m-d H:i:s');
		file_put_contents($filename, "[" . $datetime_format . "] " . json_encode($error) . "\r\n", 8);
	}



}