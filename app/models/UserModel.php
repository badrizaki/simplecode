<?php

use system\DB;

class UserModel extends DB
{
	function __construct()
	{
		parent::__construct();
	}

	public function userList()
	{
		$result = $this->getList("tbl_user"); // SELECT * FROM tbl_user
		return $result;
	}
}
?>