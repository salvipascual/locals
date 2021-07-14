<?php

namespace Locals;

use \Exception;

class Alert extends \Exception
{
	public $error;
	public $code;
	public $message;

	/**
	 * Quick way to create a new alert
	 *
	 * @param string $code
	 * @param string $message
	 * @param Array|null $payload
	 * @author salvipascual
	 */
	public function __construct($code, $message, $payload=null)
	{
		// generic error if no error code was passed
		if (empty($code)) {
			$code = '599';
		}

		// check if is an error code
		$codeStartNumber = ((string) $code)[0];
		$isError = !in_array($codeStartNumber, ['2','4']);

		// fill the class
		$this->error = $isError;
		$this->code = $code;
		$this->message = $message;
		$this->payload = $payload;
	}

	/**
	 * Teach the class how to return the alert as JSON String
	 *
	 * @return String
	 * @author salvipascual
	 */
	public function __toString()
	{
		// create JSON response
		return json_encode([
			'error' => $this->error,
			'code' => $this->code,
			'message' => $this->message,
			'payload' => ($this->payload) ? $this->payload : ""
		], JSON_THROW_ON_ERROR);
	}
}
