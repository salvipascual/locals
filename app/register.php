<?php

use Locals\Alert;
use Locals\Database;

// payload example:
// {"token":false, "username":"salvipascual"}

class Controller
{
	public $loginRequired = false;

	public function main()
	{
		// check username exist
		if(empty($this->payload->username)) {
			return new Alert(600, "Username field do not exist or is blank.");
		}

		// check if the username is not taken
		$isUsernameTaken = Database::queryFirst("
			SELECT COUNT(id) AS cnt
			FROM user
			WHERE username = '{$this->payload->username}'");

		// if username is taken
		if($isUsernameTaken->cnt > 0) {
			return new Alert(601, "Username is taken.", ["username"=>$this->payload->username]);
		}

		// create the row in the database
		Database::query("INSERT INTO user (username) VALUES ('{$this->payload->username}')");

		// return OK message
		return new Alert(200, "User has been registered.", ["username"=>$this->payload->username]);
	}
}
