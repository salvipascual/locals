<?php

class Controller
{
	public $loginRequired = false;

	public function main()
	{
		header("Location: /test.html");
		exit;
	}
}
