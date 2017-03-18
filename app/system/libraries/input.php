<?php namespace system\libraries;

/**
  *  Name         : Input
  *  Description  : Get data from input with method GET,POST or REQUEST for all method.
  *  @copyright   : Badri Zaki
  *  @version     : 0.5, 2016
  *  @author      : Badri Zaki - badrizaki@gmail.com
  *	 @package	  : get, post, request
**/

class input
{
	function __construct()
	{
	}

	/**
	* Input with method GET
	* @param $param = 'input'; input parameter
	* @param $func = 'filter_var'; function, for now only two function filter_var and trim
	*     or $func = 'trim'; function, for now only two function filter_var and trim
	* 	  or $func = 'filter_var|trim'; function, for now only two function filter_var and trim
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function get($param='', $func = FALSE)
	{
		$result = isset($_GET[$param])?$_GET[$param]:'';
		if ($func)
		{
			$filterArr = explode("|", $func);
			foreach ($filterArr as $key => $rules)
			{
				if ($rules === '1' || $rules == 'filter' || $rules == 'filter_var')
					$result = filter_var($result, FILTER_SANITIZE_STRING);
				
				if ($rules == 'trim')
					$result = trim($result);
			}
		}

		return $result;
	}

	/**
	* Input with method POST
	* @param $param = 'input'; input parameter
	* @param $func = 'filter_var'; function, for now only two function filter_var and trim
	*     or $func = 'trim';
	* 	  or $func = 'filter_var|trim'; Multy Function
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function post($param='', $func = FALSE)
	{
		$result = isset($_POST[$param])?$_POST[$param]:'';
		if ($func)
		{
			$filterArr = explode("|", $func);
			foreach ($filterArr as $key => $rules)
			{
				if ($rules === '1' || $rules == 'filter' || $rules == 'filter_var')
					$result = filter_var($result, FILTER_SANITIZE_STRING);
				
				if ($rules == 'trim')
					$result = trim($result);
			}
		}

		return $result;
	}

	/**
	* Input with All method (POST or GET)
	* @param $param = 'input'; input parameter
	* @param $func = 'filter_var'; function, for now only two function filter_var and trim
	*     or $func = 'trim'; function, for now only two function filter_var and trim
	* 	  or $func = 'filter_var|trim'; function, for now only two function filter_var and trim
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function request($param='', $func = FALSE)
	{
		$result = isset($_REQUEST[$param])?$_REQUEST[$param]:'';
		if ($func)
		{
			$filterArr = explode("|", $func);
			foreach ($filterArr as $key => $rules)
			{
				if ($rules === '1' || $rules == 'filter' || $rules == 'filter_var')
					$result = filter_var($result, FILTER_SANITIZE_STRING);
				
				if ($rules == 'trim')
					$result = trim($result);
			}
		}

		return $result;
	}
}