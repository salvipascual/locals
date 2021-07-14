<?php

namespace Locals;

use Exception;
use mysqli;

class Database
{
	public const CACHE_HOUR = 'YmdH';
	public const CACHE_DAY = 'Ymd';
	public const CACHE_MONTH = 'Ym';
	public const CACHE_YEAR = 'Y';

	private static $db;

	/**
	 * Creates a new connection
	 *
	 * @return \mysqli
	 * @author salvipascual
	 */
	private static function db(): mysqli
	{
		// return active connection
		if (self::$db !== null) {
			return self::$db;
		}

		// get the config for the host
		$config = Config::pick('database');
		$host = $config['host'];
		$user = $config['user'];
		$pass = $config['pass'];
		$name = $config['name'];

		// throw exception if error
		mysqli_report(MYSQLI_REPORT_STRICT);

		try {
			// connect to the database
			self::$db = new mysqli($host, $user, $pass, $name);
		} catch (Exception $e) {
			throw new Alert($e->getMessage(), $e->getCode());
		}

		self::$db->set_charset('utf8mb4');

		// return the connection
		return self::$db;
	}

	/**
	 * Query the database and returns an array of objects
	 * Please use escape() for all texts before creating the $sql
	 *
	 * @param string $sql : sql query
	 * @return mixed list of rows or LAST_ID if insert, false on error
	 * @author salvipascual
	 */
	public static function query($sql)
	{
		//
		// for selects
		//
		if (stripos(trim($sql), 'select') === 0) {
			// query the database
			$res = self::db()->query($sql);

			// if query found no data
			if(empty($res)) return [];

			// convert to array of objects
			$rows = [];
			while ($data = $res->fetch_object()) {
				$rows[] = $data;
			}

			$res->free_result();

			return $rows;
		}

		//
		// for insert, update and delete
		//

		// query the database and clean responses
		$res = self::db()->multi_query($sql);
		while (self::db()->more_results() && self::db()->next_result()) {
			self::db()->store_result();
		}

		// return last inserted id
		return self::db()->insert_id;
	}

	/**
	 * Query the database, returns the first response, or NULL.
	 * NOTE: Useful to search for ID's or COUNT(*). Use only for SELECT
	 *
	 * @author salvipascual
	 * @param String $sql, sql query
	 * @return Object | NULL
	 */
	public static function queryFirst($sql)
	{
		// query the database
		$res = self::query($sql);

		// return the first result, or NULL
		return empty($res) ? NULL : $res[0];
	}

	/**
	 * Query the database, returs an array of objects and cache the results based on the time passed
	 *
	 * @param String $sql , sql query
	 * @param String $limit , Database::[CACHE_HOUR|CACHE_DAY|CACHE_MONTH|CACHE_YEAR]
	 *
	 * @return mixed list of rows or LAST_ID if insert, false on error
	 * @throws \Framework\Alert
	 * @author salvipascual
	 */
	public static function queryCache($sql, $limit = self::CACHE_HOUR)
	{
		// try to pull from the cache
		$cache = TEMP_PATH .'/queries/'. date($limit) .'_'. md5($sql);
		if (file_exists($cache)) {
			$data = unserialize(file_get_contents($cache));
		}

		// get from the database and save cache
		else {
			$data = self::query($sql);
			file_put_contents($cache, serialize($data));
		}

		return $data;
	}

	/**
	 * Escape dangerous strings before passing it to mysql
	 *
	 * @param String $str , text to scape
	 * @param bool $cut , number of chars to truncate the string
	 * @return String, escaped text ready to be sent to mysql
	 * @author salvipascual
	 */
	public static function escape($str, $cut = false)
	{
		// get the escaped string
		$safeStr = self::db()->real_escape_string($str);

		// remove the ' at the beginning and end of the string
		$safeStr = trim($safeStr, "'");

		// cut the string if a max number is passed
		if ($cut) {
			$safeStr = trim(mb_substr($safeStr, 0, $cut));
		}

		return rtrim($safeStr, "\\");
	}

	/**
	 * Close the connection
	 *
	 * @author salvipascual
	 */
	public static function close()
	{
		if (self::$db !== null) {
			self::$db->close();
			self::$db = null;
		}
	}
}
