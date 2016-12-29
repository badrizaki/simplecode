<?php

use system\Model;

class UserModel extends Model
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