<?php namespace system\libraries;

class input
{
	function __construct()
	{
	}

	public function get($param='', $filter = FALSE)
	{
		$result = isset($_GET[$param])?$_GET[$param]:'';

		if ($filter)
			$result = filter_var($result, FILTER_SANITIZE_STRING);

		return $result;
	}

	public function post($param='', $filter = FALSE)
	{
		$result = isset($_POST[$param])?$_POST[$param]:'';

		if ($filter)
			$result = filter_var($result, FILTER_SANITIZE_STRING);

		return $result;
	}

	public function request($param='', $filter = FALSE)
	{
		$result = isset($_REQUEST[$param])?$_REQUEST[$param]:'';

		if ($filter)
			$result = filter_var($result, FILTER_SANITIZE_STRING);

		return $result;
	}
}