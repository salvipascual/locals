<?php

use Locals\Alert;
use Locals\Database;

// payload example:
// {
//	"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjIwNyIsInVzZXJuYW1lIjoic2FsdmlwYXNjdWFsIn0.4PEbeKC25589B9J5q9-mMyWMfDAPd5ys2puS3i8OKKQ", 
//	"start_time":"2021-07-10 00:00:00",
//	"end_time":"2021-07-15 10:30:00",
//	"sort":"recent",
//	"page":0
// }

class Controller
{
	public function main()
	{
		// prepare default data
		// NOTE: some validations are a improvement opportunity
		if(empty($this->payload->start_time)) $this->payload->start_time = "1900-01-01 00:00:00";
		if(empty($this->payload->end_time)) $this->payload->end_time = "CURRENT_TIMESTAMP";
		$this->payload->sort = (strtolower($this->payload->sort) == "oldest") ? "DESC" : "ASC";
		if(empty($this->payload->page)) $this->payload->page = 0;

		// get all posts
		// NOTE: use JOIN to speed results
		// use queryCache to cache results for one hour
		$posts = Database::queryCache("
			SELECT A.* 
			FROM post A
			LEFT JOIN user_mute B
			ON A.user_id = B.user_id
			WHERE B.mute_id <> {$this->payload->user->id}
			AND  A.updated_at >= '{$this->payload->start_time}'
			AND A.updated_at <= '{$this->payload->end_time}'
			ORDER BY A.updated_at {$this->payload->sort}
			LIMIT {$this->payload->page}, 20");

		// NOTE: An improvement here is to create a class
		// (model) that format and the information rather 
		// than sending directly the database results.

		// return OK message
		return new Alert(200, "OK", ["count"=>count($posts), "posts"=>$posts]);
	}
}
