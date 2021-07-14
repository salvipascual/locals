<?php

use Locals\Alert;
use Locals\Config;
use Locals\Database;
use Firebase\JWT\JWT;

// payload example:
// {"token":false, "username":"salvipascual", "password":"MySecureHash"}

class Controller
{
	public $loginRequired = false;

	public function main()
	{
		// check username and password exist
		// note: no pass hash on the db, ommit for now
		$loggedUser = Database::queryFirst("
			SELECT id
			FROM user
			WHERE username = '{$this->payload->username}'");
			// AND password = MD5('{$this->payload->password}')

		// if username and pass combination do not exist
		if(empty($loggedUser->id)) {
			return new Alert(602, "Username or password incorrect.");
		}

		// get the sign key
		$key = Config::pick('secure')['key'];

		// create the JWT payload
		$payload = [
			"id" => $loggedUser->id,
			"username" => $this->payload->username,
		];

		// build the login token
		$token = JWT::encode($payload, $key);

		// return OK message
		return new Alert(200, "Session started correctly.", ["token"=>$token]);
	}
}
