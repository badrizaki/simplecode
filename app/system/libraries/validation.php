<?php namespace system\libraries;

/**
  *  Name         : Validation Param
  *  Description  : This lib for validation parameter.
  *  @copyright   : Badri Zaki
  *  @version     : 0.7, 2016
  *  @author      : Badri Zaki - badrizaki@gmail.com
  *	 @package	  : validateField, filterString, onlyNumeric, numberDecimalFormat, onlyLetter, username, nim, password,
  *					dateFormat, emailFormat, statusFormat, roleCode, orderType, orderBy
**/

class validation
{
	/**
	* FOR VALIDATION FIELD DATABASE
	* @param $selector = array("field1", "Field2");
	* @param $field = array("field1", "Field22");
	* @return Array ( [1] => Field2 )
	* Field2 is not valid
	**/
	public function validateField($selector = '', $field = array())
	{
		return array_diff($selector, $field);
	}

	/**
	* FOR FILTER TAG HTML AND REMOVE
	* @param $param = "<h1>Hello World</h1>";
	* @return Hello World
	*/
	public function filterString($param='', $field='', $rule='')
	{
		if ($param == '' AND $rule == 'required')
		{
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			$result = filter_var($param, FILTER_SANITIZE_STRING);
			return array(true, "200", $result);
		}
	}

	/**
	* Validation numeric only
	* @param $param = '12345';
	* @param $field = 'namefield'; for message
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function onlyNumeric($param='', $field='', $rule='')
	{
		if ($param != "" AND !preg_match('/^[0-9]*$/',$param))
		{
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Hanya angka)";
			return array(false,$statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}

	/**
	* Validation numeric decimal
	* @param $param = '123,45';
	* @param $field = 'namefield'; for message
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function numberDecimalFormat($param='', $field='', $rule='')
	{
		if ($param != "" AND !preg_match('/^[0-9.,]*$/',$param))
		{
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Hanya angka atau desimal)";
			return array(false,$statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}

	/**
	* Validation letter/alphabet
	* @param $param = 'abcd efgh ijkl';
	* @param $field = 'namefield'; for message
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function onlyLetter($param='', $field='', $rule='')
	{
		if ($param != "" AND !preg_match('/^[a-z][A-Za-z0-9_\s-]*$/i',$param))
		// if ($param != "" AND !preg_match('/^[a-z][a-z\s-]*$/i',$param))
		{
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Hanya huruf dan spasi)";
			return array(false,$statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}

	/**
	* Validation Username
	* @param $param = 'abcd_efghijkl';
	* @param $field = 'namefield'; for message
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function username($param='', $field='Username', $rule='')
	{

		if ($param != "" AND !preg_match('/^[A-Za-z0-9_]+$/',$param))
		{
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Hanya huruf, angka dan garis bawah(_))";
			return array(false,$statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}

	/**
	* Validation Text and numeric
	* @param $param = 'abcd123';
	* @param $field = 'namefield'; for message
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function onlyText($param='', $field='', $rule='')
	{
		if ($param != "" AND !preg_match('/^[A-Za-z0-9]+$/',$param))
		{
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Hanya huruf dan angka)";
			return array(false,$statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}

	/**
	* Validation Password
	* @param $param = '123456AbCde';
	* @param $length = '9';
	* @param $field = 'namefield'; for message
	* @param $require = 'upper|number'; defult allcase|number
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function password($param='', $length=7, $field='Password', $require = "allcase|number", $rule='')
	{
		$case 	= preg_match('@[A-Za-z]@', $param);

		if ($require == 'lower' || (strpos($require, 'lower') !== false))
		{
			$case 	= preg_match('@[a-z]@', $param);
		}
		elseif ($require == 'upper' || (strpos($require, 'upper') !== false))
		{
			$case 	= preg_match('@[A-Z]@', $param);
		}

		if ($require == 'number' || (strpos($require, 'number') !== false))
		{
			$number = preg_match('@[0-9]@', $param);
		}

		if ($param != "" AND strlen($param) < $length)
		{
			$status = false;
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Minimal $length karakter)";
		} elseif ($param != "" AND (!$case OR !$number)) {
			$status = false;
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Harus kombinasi huruf dan angka)";
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			$status = true;
			$statusCode = "200";
			$statusDesc = "Valid";
		}
		return array($status, $statusCode, $statusDesc);
	}

	/**
	* Validation Format date date("Y-m-d") 0000-00-00
	* @param $param = '2017-02-07';
	* @param $field = 'namefield'; for message
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function dateFormat($param='', $field='Tanggal', $rule='')
	{
		if ($param != "" AND !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$param))
		{
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Tahun-bulan-tanggal)";
			return array(false,$statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}

	/**
	* Validation email format
	* @param $param = 'user@domain.com';
	* @param $field = 'namefield'; for message
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function emailFormat($param='', $field='email', $rule='')
	{
		if ($param != "" AND !filter_var($param, FILTER_VALIDATE_EMAIL))
		{
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid";
			return array(false,$statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}

	/**
	* Validation status or flag format only 0/1
	* @param $param = '0';
	* @param $field = 'namefield'; for message
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function statusFormat($param='', $field='', $rule='')
	{
		if ($param != "" AND !preg_match('/^(0|1)$/i',$param))
		{
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Hanya angka 0 atau 1)";
			return array(false,$statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}

	/**
	* Validation ortder type for database ASC/DESC
	* @param $param = 'DESC';
	* @param $field = 'namefield'; for message
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function orderType($param='', $field='', $rule='')
	{
		if ($param != "" AND !preg_match('/^(ASC|DESC)$/i',$param))
		{
			$statusCode = "008";
			$statusDesc = "Format $field tidak valid (Hanya ASC atau DESC)";
			return array(false,$statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}

	/**
	* Validation ortder by for database ASC/DESC
	* @param $param = 'field1';
	* @param $field = array('field1');
	* @return array($status,$statusCode, $statusDesc);
	*/
	public function orderBy($param='', $field=array(), $rule='')
	{
		## if ($param != "" AND !in_array($param, $field))
		if ($param != "" AND !preg_grep("/$param/i", $field))
		{
			$statusCode = "008";
			$statusDesc = "Field $param tidak ada dalam tabel";
			return array(false, $statusCode, $statusDesc);
		} elseif ($param == '' AND $rule == 'required') {
			return array(false, "009", "Input $field tidak boleh kosong");
		} else {
			return array(true, "200", "Valid");
		}
	}
}