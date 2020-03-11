<?php
include_once("Utilities.php");

class Repository {
	public static function checkUser($parameters) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}
		if(!$parameters['app']) {
			return false;
		}
		$stmt = $conn->prepare("SELECT u.id as id, u.role_id as role_id, u.password as password FROM user u 
			INNER JOIN app a ON a.id=u.app_id
			WHERE u.username=:username AND a.name=:app LIMIT 1");
		$stmt->bindParam(":username", $parameters['username'], PDO::PARAM_STR); 
		//$stmt->bindParam(":password", $parameters['password'], PDO::PARAM_STR); 
		$stmt->bindParam(":app", $parameters['app'], PDO::PARAM_STR); 
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$stmt->rowCount() > 0) {
			return false;
		}
		if(!password_verify($parameters['password'], $user['password'])) {
			return false;
		}
		$session_id = uniqid($user['id'] . "-");
		$session_id = self::updateSession($user['id'], $session_id);
		$result = array(
			'user' => $user,
			'session_id' => $session_id,
		);
		return $result;
	}

	private function updateSession($user_id, $session_id) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}

		$now = new DateTime();
		$date_format = $now->format("Y-m-d H:i:s");

		$stmt = $conn->prepare("SELECT id FROM user_settings WHERE user_id=:user_id LIMIT 1");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT); 
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$stmt->rowCount() > 0) {
			$stmt = $conn->prepare("INSERT INTO user_settings(user_id, session_id, last_request_at) VALUES (:user_id, :session_id, :last_request_at)");
			$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT); 
			$stmt->bindParam(":session_id", $session_id, PDO::PARAM_STR); 
			$stmt->bindParam(":last_request_at", $date_format, PDO::PARAM_STR); 
			$stmt->execute();
		} else {
			$stmt = $conn->prepare("UPDATE user_settings SET session_id=:session_id, last_request_at=:last_request_at WHERE user_id=:user_id");
			$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT); 
			$stmt->bindParam(":session_id", $session_id, PDO::PARAM_STR); 
			$stmt->bindParam(":last_request_at", $date_format, PDO::PARAM_STR); 
			$stmt->execute();
		}

		return $session_id;
	}

	public static function checkSession($parameters) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}

		$stmt = $conn->prepare("SELECT user_id, last_request_at FROM user_settings WHERE session_id=:session_id LIMIT 1");
		$stmt->bindParam(":session_id", $parameters['session_id'], PDO::PARAM_INT); 
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$stmt->rowCount() > 0) {
			return false;
		} 
		$last_request_at = $result['last_request_at'];
		$date_last_request_at = new DateTime($last_request_at);
		$now = new DateTime();
		$diff = $now->diff($date_last_request_at);
		$diff_format = $diff->format("%i");
		$session_timeout = Utilities::getParams('session_timeout');
		if(intval($diff_format) > $session_timeout) {
			return false;
		}
		self::updateSession($result['user_id'], $parameters['session_id']);
		return $parameters['session_id'];
	}


	public static function getUserRights($user_data) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}

		$stmt = $conn->prepare("SELECT ar.name as user_right FROM access_right ar
		 	INNER JOIN user_access_right uar ON uar.access_right_id=ar.id
		 	INNER JOIN user u ON u.id=uar.user_id
			WHERE u.id=:user_id");
		$stmt->bindParam(":user_id", $user_data['id'], PDO::PARAM_INT); 
		$stmt->execute();
		$user_rights = $stmt->fetchAll(PDO::FETCH_COLUMN);
		if (!$stmt->rowCount() > 0) { // if user rights not defined, get those defined for user role
			$stmt = $conn->prepare("SELECT ar.name as user_right FROM access_right ar
		 	INNER JOIN role_access_right rar ON rar.access_right_id=ar.id
		 	INNER JOIN role r ON r.id=rar.role_id
			WHERE r.id=:role_id");
			$stmt->bindParam(":role_id", $user_data['role_id'], PDO::PARAM_STR); 
			$stmt->execute();
			$user_rights = $stmt->fetchAll(PDO::FETCH_COLUMN);
		}
		return $user_rights;
	}

	public static function editUser($parameters) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}
		$stmt = "";
		if(isset($parameters['session_id'])) {
			$stmt = $conn->prepare("SELECT u.id as user_id FROM user u 
				INNER JOIN user_settings us ON us.user_id=u.id
				WHERE us.session_id=:session_id LIMIT 1");
				$stmt->bindParam(":session_id", $parameters['session_id'], PDO::PARAM_STR);

		} else if (isset($parameters['user_id'])) {
			$stmt = $conn->prepare("SELECT u.id as user_id FROM user u 
				WHERE u.id=:user_id LIMIT 1");
			$stmt->bindParam(":user_id", $parameters['user_id'], PDO::PARAM_INT);
		}
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$stmt->rowCount() > 0) {
			return false;
		}
		$stmt = $conn->prepare("UPDATE user SET password=:password WHERE id=:user_id");
		$stmt->bindParam(":user_id", $user['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(":password", $parameters['password'], PDO::PARAM_STR);
		$stmt->execute(); 

		return true;
	}

	public static function manageUser($parameters) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}
		$errors = array();
		$user_rights = unserialize($parameters['user_access_rights']);
		$stmt = $conn->prepare("SELECT uar.access_right_id as access_right_id FROM user_access_right uar 
			WHERE uar.user_id=:user_id");
		$stmt->bindParam(":user_id", $parameters['user_id'], PDO::PARAM_INT); 
		$stmt->execute();
		$db_user_rights = $stmt->fetchAll(PDO::FETCH_COLUMN);
		foreach($user_rights as $user_right) {
			if(!in_array($user_right, $db_user_rights)) {
				$stmt = $conn->prepare("INSERT INTO user_access_right(user_id, access_right_id) VALUES (:user_id, :access_right_id)");
				$stmt->bindParam(":user_id", $parameters['user_id'], PDO::PARAM_INT);
				$stmt->bindParam(":access_right_id", $user_right, PDO::PARAM_INT);
				$stmt->execute(); 
			} else {
				$errors[] = "Value exists already!";
			}
		}
		
		if(count($errors) > 0) {
			$result['errors'] = $errors;
			return $result;
		}
		return true;
		
	}

	public static function addUser($parameters) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}

		$errors = array();
		$stmt = $conn->prepare("SELECT u.id as user_id FROM user u
			WHERE u.username=:username AND u.app_id=:app_id LIMIT 1");
		$stmt->bindParam(":username", $parameters['username'], PDO::PARAM_STR);
		$stmt->bindParam(":app_id", $parameters['app_id'], PDO::PARAM_INT); 
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount() > 0) {
			$errors[] = "Username already exists!";
			return $errors;
		}
		$stmt = $conn->prepare("INSERT INTO user (username, password, role_id, app_id) VALUES (:username, :password, :role_id, :app_id)");
		$stmt->bindParam(":username", $parameters['username'], PDO::PARAM_STR);
		$stmt->bindParam(":password", $parameters['password'], PDO::PARAM_STR);
		$stmt->bindParam(":role_id", $parameters['role'], PDO::PARAM_INT); 
		$stmt->bindParam(":app_id", $parameters['app'], PDO::PARAM_INT); 
		$stmt->execute();

		return true;
		
	}

	public static function getUsers() { // all users no matter the role
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}
		$stmt = $conn->prepare("SELECT u.id as user_id, u.username as username, u.role_id FROM user u");
		$stmt->execute();
		$users = $stmt->fetchAll();
		$data = array();
		foreach($users as $user) {
			$data[$user['user_id']]['user_id'] = $user['user_id'];
			$data[$user['user_id']]['username'] = $user['username'];
			$role_access_rights = self::getAccessRights($user['role_id']); // get existing access rights per user role
			$user_access_rights = self::getAccessRights(null, $user['user_id']); // get existing access rights per user
			if($user_access_rights) {
				foreach($user_access_rights as $user_access_right) {
					$data[$user['user_id']]['user_access_rights'][$user_access_right['id']] = $user_access_right['name'];
				}
			}	
			if($role_access_rights) {
				foreach($role_access_rights as $role_access_right) {
					$data[$user['user_id']]['role_access_rights'][$role_access_right['id']] = $role_access_right['name'];
				}
			}	
		}
		
		return $data;
	}

	public static function getAccessRights($role_id, $user_id=null) {
		if(!$role_id && !$user_id) {
			return false;
		}
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}
		if($role_id) {
			$stmt = $conn->prepare("SELECT ar.id as id, ar.name as name FROM access_right ar 
			INNER JOIN role_access_right rar ON rar.access_right_id=ar.id 
			INNER JOIN role r ON r.id=rar.role_id WHERE r.id=:role_id");
			$stmt->bindParam(":role_id", $role_id, PDO::PARAM_INT); 
		} else if($user_id) {
			$stmt = $conn->prepare("SELECT ar.id as id, ar.name as name FROM access_right ar 
			INNER JOIN user_access_right uar ON uar.access_right_id=ar.id 
			INNER JOIN user u ON u.id=uar.user_id WHERE u.id=:user_id");
			$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT); 
		}

		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}

	public static function getApps() {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}
		$stmt = $conn->prepare("SELECT a.id as app_id, a.name as app_name FROM app a");
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}
	public static function getRoles($app = false) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}

		$stmt = $conn->prepare("SELECT r.id as role_id, r.name as role_name FROM role r");
		if($app) {
			$stmt = $conn->prepare("SELECT r.id as role_id, r.name as role_name, a.id as app_id FROM role r
			INNER JOIN app_role ar ON ar.role_id=r.id
			INNER JOIN app a ON a.id=ar.app_id");
		} 
		
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	public static function addApp($parameters) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}
		$errors = array();
		$stmt = $conn->prepare("SELECT a.id as app_id FROM app a
			WHERE a.name=:app_name LIMIT 1");
		$stmt->bindParam(":app_name", $parameters['app'], PDO::PARAM_STR);
		$stmt->execute();
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount() > 0) {
			$errors[] = "App already exists!";
			return $errors;
		}
		$stmt = $conn->prepare("INSERT INTO app (name) VALUES (:app_name)");
		$stmt->bindParam(":app_name", $parameters['app'], PDO::PARAM_STR); 
		$stmt->execute();

		return true;
		
	}

	public static function addRole($parameters) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}
		$errors = array();
		// check if role exists already
		$stmt = $conn->prepare("SELECT r.id as id FROM role r
			WHERE r.name=:role_name LIMIT 1");
		$stmt->bindParam(":role_name", $parameters['role'], PDO::PARAM_STR);
		$stmt->execute();
		$role = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount() > 0) {
			$stmt = $conn->prepare("SELECT ar.id as app_role_id FROM app_role ar
				WHERE ar.role_id=:role_id AND ar.app_id=:app_id LIMIT 1");
			$stmt->bindParam(":role_id", $role['id'], PDO::PARAM_INT);
			$stmt->bindParam(":app_id", $parameters['app_id'], PDO::PARAM_INT);
			$stmt->execute();
			//$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($stmt->rowCount() > 0) {
				$errors[] = "Role already exists!";
				return $errors;
			}
		} else {
			$stmt = $conn->prepare("INSERT INTO role (name) VALUES (:role_name)");
			$stmt->bindParam(":role_name", $parameters['role'], PDO::PARAM_STR); 
			$stmt->execute();
			$role['id']=$conn->lastInsertId();
		}
		$stmt = $conn->prepare("INSERT INTO app_role (app_id, role_id) VALUES (:app_id, :role_id)");
		$stmt->bindParam(":app_id", $parameters['app_id'], PDO::PARAM_INT);
		$stmt->bindParam(":role_id", $role['id'], PDO::PARAM_INT); 
		$stmt->execute();		

		return true;
	}

	public static function addAccessRight($parameters) {
		$conn = Utilities::getDatabaseConnection();
		if(!$conn) {
			return false;
		}
		$errors = array();
		$stmt = $conn->prepare("SELECT a.id as id FROM access_right a
			WHERE a.name=:access_right LIMIT 1");
		$stmt->bindParam(":access_right", $parameters['access_right'], PDO::PARAM_STR);
		$stmt->execute();
		$access_right = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($stmt->rowCount() > 0) {
			$stmt = $conn->prepare("SELECT rar.id as role_access_right_id FROM role_access_right rar
				WHERE rar.role_id=:role_id AND rar.access_right_id=:access_right_id LIMIT 1");
			$stmt->bindParam(":access_right_id", $access_right['id'], PDO::PARAM_INT);
			$stmt->bindParam(":role_id", $parameters['role_id'], PDO::PARAM_INT);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$errors[] = "Access right already exists!";
				return $errors;
			}
		} else {
			$stmt = $conn->prepare("INSERT INTO access_right (name) VALUES (:access_right_name)");
			$stmt->bindParam(":access_right_name", $parameters['access_right'], PDO::PARAM_STR); 
			$stmt->execute();
			$access_right['id']=$conn->lastInsertId();
		}
		$stmt = $conn->prepare("INSERT INTO role_access_right (access_right_id, role_id) VALUES (:access_right_id, :role_id)");
		$stmt->bindParam(":access_right_id", $access_right['id'], PDO::PARAM_INT); 
		$stmt->bindParam(":role_id", $parameters['role_id'], PDO::PARAM_INT);
		$stmt->execute();
		
		return true;
		
	}



}
