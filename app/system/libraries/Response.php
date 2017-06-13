<?php namespace system\libraries;

/**
  *  Name         : Response
  *  Description  : Print response.
  *  @copyright   : Badri Zaki
  *  @version     : 0.5, 2016
  *  @author      : Badri Zaki - badrizaki@gmail.com
  *	 @package	  : json
**/

class Response
{
	function __construct()
	{
	}

	public function json($response = '')
	{
		header('Content-Type: application/json');
		echo json_encode($response);
	}
}