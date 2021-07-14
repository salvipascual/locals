<?php

use Locals\Alert;
use Locals\Database;

// payload example:
// {
//	"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjIwNyIsInVzZXJuYW1lIjoic2FsdmlwYXNjdWFsIn0.4PEbeKC25589B9J5q9-mMyWMfDAPd5ys2puS3i8OKKQ", 
//	"block_id":10
// }

class Controller
{
	public function main()
	{
		// check if the user ID exists
		$isUserExist = Database::queryFirst("
			SELECT COUNT(id) AS cnt
			FROM user
			WHERE id = {$this->payload->user_id}");

		// if user ID do not exist
		if($isUserExist->cnt <= 0) {
			return new Alert(601, "User ID do not exist.", ["user_id"=>$this->payload->user_id]);
		}

		// block user ID 
		// NOTE: use IGNORE to avoid duplicate errors
		$oneMoreMonth = date("Y-m-d H:m:s", strtotime("+1 month"));
		Database::query("
			INSERT IGNORE INTO user_mute (user_id, mute_id, expired_at) 
			VALUES ({$this->payload->user->id}, {$this->payload->user_id}, '$oneMoreMonth')");

		// return OK message
		return new Alert(200, "OK");
	}
}
