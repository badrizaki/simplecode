<?php namespace system\libraries;

use stdClass;
use system\libraries\Response;
use system\libraries\visitor;

class StatusExecution
{
	function __construct()
	{
        /* GET CONFIG FILE */
        global $config;
        $this->config   = $config;

		$this->response = new Response();
		$this->lib = new stdClass();
		$this->lib->file     = new file();
		$this->lib->visitor  = new visitor();

		$this->visitor 		= $this->lib->visitor->setVisitorInfo($this->config["ipVisitor"]);
		$this->ip_addr 		= $_SERVER["REMOTE_ADDR"];
		$this->logDirError  = $this->config["logApi"].'/error/';
		$this->logDirAccess = $this->config["logApi"].'/access/';
	}

	public function databaseError($result = array(), $filename = __FUNCTION__)
	{
		$result = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "006",
			"statusDesc"	=> $result,
			"message"		=> $this->config["statusCode"]["006"],
			"data"			=> ''
		);
		$logFileName = $filename."_error_".date("d-m-Y").".log";
		$this->lib->file->createLog($logFileName, $result, $this->logDirError);

		$response = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "006",
			"statusDesc"	=> 'Database error',
			"message"		=> $this->config["statusCode"]["006"],
			"data"			=> ''
		);
		$this->response->json($response);
		exit;
	}

	public function databaseEmpty($result = array(), $filename = __FUNCTION__)
	{
		$response = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "014",
			"statusDesc"	=> $result,
			"message"		=> $this->config["statusCode"]["014"],
			"data"			=> ''
		);
		$logFileName = $filename."_error_".date("d-m-Y").".log";
		$this->lib->file->createLog($logFileName, $response, $this->logDirError);
		$this->response->json($response);
		exit;
	}

	public function unknownField($field = array(), $filename = __FUNCTION__)
	{
		$response = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "005",
			"statusDesc"	=> "Unknown Field (".implode(",",$field).")",
			"message"		=> $this->config["statusCode"]["005"],
			"visitor"		=> $this->visitor
		);
		$logFileName = $filename."_error_".date("d-m-Y").".log";
		$this->lib->file->createLog($logFileName, $response, $this->logDirError);
		$this->response->json($response);
		exit;
	}

	public function valueNull($message = 'ID Kosong', $filename = __FUNCTION__)
	{
		$response = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "009",
			"statusDesc"	=> $this->config["statusCode"]["009"],
			"message"		=> $message,
			"visitor"		=> $this->visitor
		);
		$logFileName = $filename."_error_".date("d-m-Y").".log";
		$this->lib->file->createLog($logFileName, $response, $this->logDirError);
		$this->response->json($response);
		exit;
	}

	public function valueNotValid($message = '', $filename = __FUNCTION__)
	{
		$response = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "008",
			"statusDesc"	=> $message,
			"message"		=> $message,
			"visitor"		=> $this->visitor
		);
		$logFileName = $filename."_error_".date("d-m-Y").".log";
		$this->lib->file->createLog($logFileName, $response, $this->logDirError);
		$this->response->json($response);
		exit;
	}

	public function passwordNotValid($message = '', $filename = __FUNCTION__)
	{
		if ($message == '') $message = $this->config["statusCode"]["011"];

		$response = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "011",
			"statusDesc"	=> $message,
			"message"		=> $message,
			"visitor"		=> $this->visitor
		);
		$logFileName = $filename."_error_".date("d-m-Y").".log";
		$this->lib->file->createLog($logFileName, $response, $this->logDirError);
		$this->response->json($response);
		exit;
	}

	public function notActive($message = '', $filename = __FUNCTION__)
	{
		if ($message == '') $message = $this->config["statusCode"]["012"];
		
		$response = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "012",
			"statusDesc"	=> $message,
			"message"		=> $message,
			"visitor"		=> $this->visitor
		);
		$logFileName = $filename."_error_".date("d-m-Y").".log";
		$this->lib->file->createLog($logFileName, $response, $this->logDirError);
		$this->response->json($response);
		exit;
	}

	public function alreadyExists($message = '', $filename = __FUNCTION__, $result = array())
	{
		if ($message == '') $message = $this->config["statusCode"]["013"];

		$result = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "013",
			"statusDesc"	=> $result,
			"message"		=> $message,
			"data"			=> ''
		);
		$logFileName = $filename."_error_".date("d-m-Y").".log";
		$this->lib->file->createLog($logFileName, $result, $this->logDirError);

		$response = array(
			"dateTime"		=> date("d-m-Y H:i:s"),
			"statusCode" 	=> "013",
			"statusDesc"	=> 'Data already exists',
			"message"		=> $message,
			"data"			=> ''
		);
		$this->response->json($response);
		exit;
	}
}