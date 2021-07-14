<?php

use Locals\Alert;
use Locals\Config;
use Locals\Database;
use Firebase\JWT\JWT;

// do not let errors break the flow
error_reporting(0);

// localize timezone and dates
setlocale(LC_TIME, "en_EN", 'English_United_States', 'English');
date_default_timezone_set('America/New_York');

// ejecute when the script is done
register_shutdown_function(static function(){
	// check if there was any issues
	$error = error_get_last();

	// if there are errors, warnings or notices, take action
	if ($error !== null) {
		ob_clean();
		echo new Alert(500, $error['message']);
	}

	// close the database connection
	Database::close();
});

// get controller and payload passed (GET or POST)
$controller = isset($_REQUEST['c']) ? strtolower($_REQUEST['c']) : "index";
$payload = isset($_REQUEST['p']) ? $_REQUEST['p'] : "";

//  global
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP_PATH', BASE_PATH . 'app/');
define('TEMP_PATH', BASE_PATH . 'tmp/');

// show 404 error
if (!file_exists(APP_PATH . "$controller.php")) {
	header("HTTP/1.0 404 Not Found");
	echo new Alert(404, "Page not found.");
	exit;
}

// add the autoload
include BASE_PATH . "vendor/autoload.php";

// supporting global constants
define('IS_PRODUCTION', Config::pick('general')['tier'] == "production");
define('SUPPORT_EMAIL', Config::pick('general')['support_email']);

// if not production display all errors
if(!IS_PRODUCTION) error_reporting(E_ALL);

// create the controller
include APP_PATH . "$controller.php";
$page = new Controller();

// force login, unless stated on the controller manually
if( ! isset($page->loginRequired)) {
	$page->loginRequired = true;
}

// build the payload and pass it to the controller
$page->payload = json_decode(base64_decode($payload));

// ensure user is signed in
if($page->loginRequired) {
	// ensure payload is correctly formatted
	if(empty($page->payload->token)) {
		throw new \Exception("Field token is required on the payload.");
	}

	// get the sign key
	$key = Config::pick('secure')['key'];

	// get info from the login token
	try {
		$page->payload->user = JWT::decode($page->payload->token, $key, ['HS256']);
	} catch (\Exception $e) {
		throw new \Exception("The login token could not be validated.");
	}
}

// run the Controller
$return = $page->main();

// ensure the controller returns an Alert
if ( ! is_a($return, 'Locals\Alert')) {
	throw new \Exception("The return type must be Alert.");
}

// display the Alert
echo $return;
