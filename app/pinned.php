<?php

use Locals\Alert;
use Locals\Database;

// payload example:
// {
//	"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjIwNyIsInVzZXJuYW1lIjoic2FsdmlwYXNjdWFsIn0.4PEbeKC25589B9J5q9-mMyWMfDAPd5ys2puS3i8OKKQ", 
// }

class Controller
{
	public function main()
	{
		// get a pinned post.
		// NOTE: random because I am not sure how else to do it
		// use queryCache to cache results for one hour
		$posts = Database::queryCache("
			SELECT A.* 
			FROM post A
			LEFT JOIN user_mute B
			ON A.user_id = B.user_id
			WHERE B.mute_id <> {$this->payload->user->id}
			ORDER BY RAND()
			LIMIT 5");

		// NOTE: An improvement here is to create a class
		// (model) that format and the information rather 
		// than sending directly the database results.

		// return OK message
		return new Alert(200, "OK", ["count"=>count($posts), "posts"=>$posts]);
	}
}
