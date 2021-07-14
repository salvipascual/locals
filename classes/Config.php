<?php

namespace Locals;

class Config
{
	private static $cnf = null;

	/**
	 * Get all the configs
	 *
	 * @author salvipascual
	 * @return array
	 */
	public static function all()
	{
		if (self::$cnf === null) {
			self::$cnf = parse_ini_file(BASE_PATH .'configs/config.ini', true, INI_SCANNER_RAW);
		}
		return self::$cnf;
	}

	/**
	 * Get the config for a category
	 *
	 * @author salvipascual
	 * @param String: config name
	 * @return array
	 */
	public static function pick($name)
	{
		return self::all()[$name];
	}
}
