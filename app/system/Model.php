<?php namespace system;

use system\DB;

/**
 * @see \system\DB
 */
class Model extends DB
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getDatabase() { return 'db'; }
}