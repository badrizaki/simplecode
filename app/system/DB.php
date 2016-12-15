<?php namespace system;

/**
  *  Name         : DB Execution.
  *  Description  : This class for execute database from model with extends this class, require database class for connection.
  *  @copyright   : Atom Media Studio
  *  @license     : This loader provided to Internal use of Atom Media Studio and it's affiliates, you are 
  *                 permitted to use this library only if you are employee of Atom Media Studio or you are an 
  *                 offcial affiliates of Atom Media Studio solely for Internal product development of Atom Media Studio, 
  *                 distributing or using this library to any unauthorized 3rd party is strictly prohibited.
  *  @version     : 2.0
  *  @author      : Badri Zaki - badrizaki@atommediastudio.com
  *

  HOW TO USE :
	class Model extends DB -> must extends this class
	$this->select('SELECTOR'); this method for selection field from table
	$this->from('TABLE'); this method set table name
	$this->where(array("field1" => "value1", "field2" => "value2")); this method for where condition,
		using array with fieldname from database as key and value
	$this->limit(10); this method for set limit
	$this->offset(1); this method for set offset
	
	if you want show one row using
		$this->get();
	and use this for more row/ fetchAll
		$this->getist();

	SAMPLE :
	GET
		$this->select('*');
		$this->from('tbl_user');
		$this->where(array('id' => 1, 'user' => 'sample'));
		$this->get();

	GET LIST
		$params['tu.userId'] = 2;
		$params['nama'] = 'tes';
		$this->select('tu.*, tul.date, tdu.exp');
		$this->from('tbl_user tu');
		$result = $this->getList('tbl_user tu');
		
	JOIN
		$this->join('tbl_user_login tul', 'tul.userId=tu.userId');
		$this->join('tbl_data_user tdu', 'tdu.userId=tu.userId');

	WHERE CONDITION
		$this->where($param, $param2, $param3, $param4, $param5);
		$this->where('mes.user_id', $param['user_id']); // Simple where result : mes.user_id=:user_id
			$param = can array or string,
					 if is array you must set key is fieldname form table
					 if string this is used for fieldname table and $param2 for value
			$param2 = if $param string this param is value, 
					  if $param is array can empty/null,
					  for bind 'userId=3||nama=tes' if $param5 false or custum where
			$param3 = CONDITION 'AND/OR'
			$param4 = OPERATOR '=<>'
			$param5 = CUSTOM where, value = boolean *sample
				$this->where('(tu.userId > 3 OR tu.nama = \'tes\')',NULL,NULL,NULL,false); // with custom condition
				$this->where('(tu.userId = :userId OR tu.nama = :nama)','userId=3||nama=tes',NULL,NULL,false);

	ORDER BY
		$this->limit($param['orderBy']);
		$this->limit($param['orderType']);

	LIMIT
		$this->limit(10);
		$this->offset($param['offset']);

	INSERT
		$data = array(
			'user' => "tes",
			'pass' => '900'
		);
		$result = $this->insert("tbl_user", $data);

	UPDATE
		$params['id'] = 2;
		$this->where($params);
		$data = array(
			'education' => "update",
			'exp' 		=> 'set',
			'userId' 	=> '2'
		);
		$result = $this->update("tbl_data_user", $data);
	
	DELETE
		$params['userId'] = 1;
		$this->where($params);
		$result = $this->delete("tbl_data_user");

	QUERY
		if with where condition using parameter for bind 
		$params['userId'] = 1;
		$result = $this->query("select * from tbl_data_user", $params);

	* NOTE this library is not finish, must modif for database sql server
**/

use config\Database;
use PDO;

abstract class DB extends Database
{
	public $select  = '*';
	public $from 	= '';
	public $where 	= array();
	public $limit 	= '';
	public $offset 	= 0;
	public $join 	= array();
	public $row 	= true;

	function __construct()
	{
		global $config;
		$this->config 	= $config;
		$this->dbType 	= isset($this->config['database']['connection'])?$this->config['database']['connection']:'';
		$this->select 	= '*';
		$this->from 	= '';
		$this->where 	= array();
		$this->condition= array();
		$this->operator	= array();
		$this->value	= array();
		$this->builder	= array();
		$this->orderBy 	= '';
		$this->orderType= 'DESC';
		$this->limit 	= '';
		$this->offset 	= 0;
		$this->join 	= array();
		$this->allRow	= true;
		$this->sql		= '';

		if (strtolower($this->dbType) == 'sqlserver')
		{
			$dbConn = $this->getConnection();
			$this->conn = sqlsrv_connect($dbConn['serverName'], $dbConn['connectionInfo']);
		}
	}

	public function select 	($SELECT = '*') 		{ $this->select = $SELECT; 	}
	public function from 	($FROM = '') 			{ $this->from 	= $FROM; 	}

	public function where($WHERE = array(), $VALUE = '', $CONDITION = 'AND', $OPERATOR = '=', $BUILDER = TRUE)
	{
		if (is_array($WHERE))
		{
			$this->where 	= $WHERE;
		}
		else {
			if ($BUILDER)
			{
				$this->where[$WHERE] = $VALUE;
				$countWhere = count($this->where);
				$this->condition[$countWhere] = $CONDITION;
				$this->operator[$countWhere] = $OPERATOR;
			}
			else {
				$countWhere = count($this->where);
				$this->builder[$countWhere] = FALSE;
				$this->where[$countWhere] = $WHERE;
				$this->value[$countWhere] = $VALUE;
			}
		}
	}

	public function order($ORDERBY = '', $ORDERTYPE = 'DESC')
	{
		$this->orderBy 		= $ORDERBY;
		$this->orderType 	= $ORDERTYPE;
	}

	public function limit 	($LIMIT = '') 			{ $this->limit 	= $LIMIT; 	}
	public function offset 	($OFFSET = 0) 			{ $this->offset	= $OFFSET; 	}
	public function join 	($JOIN = '', $ON = '') 	{ $this->join[$JOIN] = $ON; }
	public function allRow 	($ALLROW = '') 			{ $this->allRow	= $ALLROW; 	}
	public function traceQuery() 					{ return $this->sql; }

	## FOR GET ONE ROW ONLY
	public function get($table = '')
	{
		$where = '';
		$join  = '';

		## SELECTOR
		if (!isset($this->select))
			$this->select = '*';

		## FROM TABLE
		if (!isset($this->from) || $this->from == '')
		{
			if ($table != '')
			{
				$this->from = $table;
			} else {
				return array("status" => "error", "result" => "Not table selection");
			}
		}

		## JOIN
		if (isset($this->join) && $this->join != '')
		{
			foreach ($this->join as $key => $value)
			{
				$join .= ' LEFT JOIN '.$key.' ON '.$value.' ';
			}
		}

		##########################
		##     SQL SERVER		##
		##########################
		if (strtolower($this->dbType) == 'sqlserver')
		{
			$data = array();

			## WHERE
			if (isset($this->where))
			{
				foreach ($this->where as $key => $value)
				{
					$fieldname = $key;
					$data[] = &$this->where[$fieldname];

					if (strpos($key, '.'))
					{
						list($as, $key) = explode('.', $key);
						$fieldname = $as.'.'.$key;
					}

					if ($where == '')
					{
						$where .= ' WHERE ' . $fieldname . '=?';
					} else {
						$where .= ' AND ' . $fieldname . '=?';
					}
				}
			}

			$sql = 'SELECT ' . $this->select . 
				   ' FROM '  . $this->from . 
				   $join .
				   $where .
				   ';';
			$this->sql = $sql;

			$stmt 	= sqlsrv_prepare($this->conn, $sql, $data);
			$row 	= sqlsrv_execute($stmt);
			if ($row)
			{
				// $result = array();
				$result = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC );
				
				if (!$result)
				{
					unset($result);
					$result = "Data empty";
				}
				
				$response = array(
					"status" => "success",
					"result" => $result
				);
			}
			else {
				$e = 'Data failed to get';
				if( ($errors = sqlsrv_errors() ) != null) {
			        foreach( $errors as $error ) {
			            $e .= '<br>'.$error[ 'message'];
			        }
			    }
				$response = array(
					"status" => "error",
					"result" => $e
				);
			}
		}
		else {
			## WHERE
			if (isset($this->where))
			{
				$i = 1;
				foreach ($this->where as $key => $value)
				{
					$fieldname = $key;

					if (strpos($key, '.'))
					{
						list($as, $key) = explode('.', $key);
						$fieldname = $as.'.'.$key;
					}

					## AND OR
					if (isset($this->condition[$i]))
					{
						$conditionDes = $this->condition[$i];
					} else {
						$conditionDes = 'AND';
					}

					## OPERATOR =<>=
					if (isset($this->operator[$i]))
					{
						$operator = $this->operator[$i];
					} else {
						$operator = '=';
					}

					if ($where == '')
					{
						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							$where .= ' WHERE ' . $value;
						} else {
							$where .= ' WHERE ' . $fieldname . $operator . ':' . $key;
						}
					}
					else {
						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							$where .= ' '.$conditionDes.' ' . $value;
						} else {
							$where .= ' '.$conditionDes.' ' . $fieldname . $operator . ':' . $key;
						}
					}
					$i++;
				}
			}

			## EXECUTE
			try{
				$sql = 'SELECT ' . $this->select . 
					   ' FROM '  . $this->from . 
					   $join .
					   $where .
					   ';';
				$this->sql = $sql;

				$db = self::getConnection();
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$stmt = $db->prepare($sql);

				if (isset($this->where))
				{
					$i = 1;
					foreach ($this->where as $key => $value)
					{
						$fieldname = $key;

						if (strpos($key, '.'))
						{
							list($as, $key) = explode('.', $key);
							$fieldname = $as.'.'.$key;
						}

						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							if (isset($this->value[$key]) && $this->value[$key] != '')
							{
								$bind = explode("||", $this->value[$key]);
								foreach ($bind as $k => $vals)
								{
									list($parameter, $resValue) = explode("=", $vals);
									$stmt->bindValue(':'.$parameter, $resValue);
								}
							}
						} else {
							if (is_string($value)) {
								$stmt->bindValue(':'.$key, $this->where[$fieldname], PDO::PARAM_STR);
							} else {
								$stmt->bindValue(':'.$key, $this->where[$fieldname]);
							}
						}

						$i++;
					}
				}

				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				$response = array(
					"status" => "success",
					"result" => $result
				);

			} catch(\PDOException $e) {
				$response = $response = array(
					"status" => "error",
					"result" => $e->getMessage()
				);
			}
		}
		return $response;
	}

	## FOR GET MULTIPLE ROW ONLY
	public function getList($table = '')
	{
		$where = '';
		$orderby = '';
		$limit = '';
		$join  = '';

		## SELECTOR
		if (!isset($this->select))
			$this->select = '*';

		## FROM TABLE
		if (!isset($this->from) || $this->from == '')
		{
			if ($table != '')
				$this->from = $table;
			else
				return array("status" => "error", "result" => "Not table selection");
		}

		## JOIN
		if (isset($this->join) && count($this->join) > 0)
		{
			foreach ($this->join as $key => $value)
			{
				$join .= ' LEFT JOIN '.$key.' ON '.$value.' ';
			}
		}

		## ORDER BY, TYPE
		if ($this->orderBy != '')
		{
			$orderBy = ' ORDER BY ' . $this->orderBy . ' ' . $this->orderType . ' ';
		}

		##########################
		##     SQL SERVER		##
		##########################
		if (strtolower($this->dbType) == 'sqlserver')
		{
			$data = array();

			## TOP
			if(isset($this->limit) && $this->limit != '')
			{
				if (!is_numeric($this->limit))
					return array("status" => "error", "result" => "Please cek your limit, numeric only");
				$limit = " TOP $this->limit ";
			}

			## WHERE
			if (isset($this->where))
			{
				foreach ($this->where as $key => $value)
				{
					$fieldname = $key;
					$data[] = &$this->where[$fieldname];

					if (strpos($key, '.'))
					{
						list($as, $key) = explode('.', $key);
						$fieldname = $as.'.'.$key;
					}

					if ($where == '')
					{
						$where .= ' WHERE ' . $fieldname . '=?';
					} else {
						$where .= ' AND ' . $fieldname . '=?';
					}
				}
			}

			$sql = 'SELECT ' . $limit . $this->select . 
				   ' FROM '  . $this->from . 
				   $join .
				   $where .
				   ';';
			$this->sql = $sql;

			$stmt 	= sqlsrv_prepare($this->conn, $sql, $data);
			$row 	= sqlsrv_execute($stmt);
			if ($row)
			{
				$result = array();
				while( $resultTemp = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ))
				{
					$result[] = $resultTemp;
				}
				
				if (!$result)
				{
					unset($result);
					$result = "Data empty";
				}
				
				$response = array(
					"status" => "success",
					"result" => $result
				);
			}
			else {
				$e = 'Data failed to get';
				if( ($errors = sqlsrv_errors() ) != null) {
			        foreach( $errors as $error ) {
			            $e .= '<br>'.$error[ 'message'];
			        }
			    }
				$response = array(
					"status" => "error",
					"result" => $e
				);
			}
		}
		else {
			## WHERE
			if (isset($this->where))
			{
				$i = 1;
				foreach ($this->where as $key => $value)
				{
					$fieldname = $key;

					if (strpos($key, '.'))
					{
						list($as, $key) = explode('.', $key);
						$fieldname = $as.'.'.$key;
					}

					## AND OR
					if (isset($this->condition[$i]) && $this->condition[$i] != NULL)
					{
						$conditionDes = $this->condition[$i];
					} else {
						$conditionDes = 'AND';
					}

					## OPERATOR =<>= in like
					if (isset($this->operator[$i]))
					{
						$operator = $this->operator[$i];
					} else {
						$operator = '=';
					}

					if ($where == '')
					{
						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							$where .= ' WHERE ' . $value;
						} else {
							$where .= ' WHERE ' . $fieldname . $operator . ':' . $key;
						}
					}
					else {
						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							$where .= ' '.$conditionDes.' ' . $value;
						} else {
							$where .= ' '.$conditionDes.' ' . $fieldname . $operator . ':' . $key;
						}
					}
					$i++;
				}
			}

			## LIMIT
			if(isset($this->limit) && $this->limit != '')
				$limit = " LIMIT :limit OFFSET :offset ";

			## EXECUTE
			try{
				$sql = 'SELECT ' . $this->select . 
					   ' FROM '  . $this->from . 
					   $join .
					   $where .
					   $orderby .
					   $limit .
					   ';';
				$this->sql = $sql;

				$db = self::getConnection();
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$stmt = $db->prepare($sql);

				if (isset($this->where))
				{
					$i = 1;
					foreach ($this->where as $key => $value)
					{
						$fieldname = $key;

						if (strpos($key, '.'))
						{
							list($as, $key) = explode('.', $key);
							$fieldname = $as.'.'.$key;
						}

						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							if (isset($this->value[$key]) && $this->value[$key] != '')
							{
								$bind = explode("||", $this->value[$key]);
								foreach ($bind as $k => $vals)
								{
									list($parameter, $resValue) = explode("=", $vals);
									$stmt->bindValue(':'.$parameter, $resValue);
								}
							}
						} else {
							if (is_string($value)) {
								$stmt->bindValue(':'.$key, $this->where[$fieldname], PDO::PARAM_STR);
							} else {
								$stmt->bindValue(':'.$key, $this->where[$fieldname]);
							}
						}

						$i++;
					}
				}

				if(isset($this->limit) && $this->limit != '')
				{
					$stmt->bindValue(':limit', $this->limit, PDO::PARAM_INT);
					$stmt->bindValue(':offset', $this->offset, PDO::PARAM_INT);
				}

				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$response = array(
					"status" => "success",
					"result" => $result
				);
			} catch(\PDOException $e) {
				$response = $response = array(
					"status" => "error",
					"result" => $e->getMessage()
				);
			}
		}
		return $response;
	}

	public function insert($table = '', $params = array())
	{
		$field  = '';
		$values = '';

		## TABLE
		if ($table == '')
			return array("status" => "error", "result" => "Not table selection");

		##########################
		##     SQL SERVER		##
		##########################
		if (strtolower($this->dbType) == 'sqlserver')
		{
			$data = array();

			## VALUE INSERT
			if (count($params) > 0)
			{
				foreach ($params as $fieldname => $value)
				{
					$data[] = &$params[$fieldname];
					if ($field == '')
					{
						$field .= $fieldname;
						$values .= '?';
					} else {
						$field .= ', '.$fieldname;
						$values .= ', ?';
					}
				}
			} else {
				return array("status" => "error", "result" => "Please set your data insert");
			}

			$sql 	= "INSERT INTO $table ($field) VALUES ($values)";
			$this->sql = $sql;
			$stmt 	= sqlsrv_prepare($this->conn, $sql, $data);
			$row 	= sqlsrv_execute($stmt);
			if ($row)
			{
				$response = array(
					"status" => "success",
					"result" => "Data success insert"
				);
			}
			else {
				$e = 'Data failed to insert';
				if( ($errors = sqlsrv_errors() ) != null) {
			        foreach( $errors as $error ) {
			            $e .= '<br>'.$error[ 'message'];
			        }
			    }
				$response = array(
					"status" => "error",
					"result" => $e
				);
			}
		}
		else {
			if (count($params) > 0)
			{
				foreach ($params as $fieldname => $value)
				{
					if ($field == '')
					{
						$field .= $fieldname;
						$values .= ':'.$fieldname;
					} else {
						$field .= ', '.$fieldname;
						$values .= ', :'.$fieldname;
					}
				}
			} else {
				return array("status" => "error", "result" => "Please set your data insert");
			}

			try{

				$db = self::getConnection();
				$db->beginTransaction();
				// $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				// $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$sql = 'INSERT INTO '.$table.' ( '.$field.' ) VALUES ( '.$values.' );
						SELECT LAST_INSERT_ID() as id FROM '.$table.';';
				$this->sql = $sql;
				$stmt = $db->prepare($sql);

				if (count($params) > 0)
				{
					foreach ($params as $fieldname => $value)
					{
						if (is_string($value)) {
							$stmt->bindValue(':'.$fieldname, $params[$fieldname], PDO::PARAM_STR);
						} else {
							$stmt->bindValue(':'.$fieldname, $params[$fieldname]);
						}
					}
				}

				$stmt->execute();
				$stmt->nextRowset();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				$db->commit();

				$response = array(
					"status" => "success",
					"result" => $result
				);
			} catch(\PDOException $e) {
				$response = $response = array(
					"status" => "error",
					"result" => $e->getMessage()
				);
			}
		}
		return $response;
	}

	public function update($table = '', $params = array())
	{
		$updateCond = '';
		$where = '';

		## TABLE
		if ($table == '')
			return array("status" => "error", "result" => "Not table selection");

		##########################
		##     SQL SERVER		##
		##########################
		if (strtolower($this->dbType) == 'sqlserver')
		{
			## UPDATE CONDITION
			if (count($params) > 0)
			{
				foreach ($params as $key => $val)
				{
					if ($val != "")
					{
						$values[] = &$params[$key];
						if ($updateCond == "")
						{
							$updateCond .= $key . "=?";
						} else {
							$updateCond .= "," . $key . "=?";
						}
					}
				}
			} else {
				return array("status" => "error", "result" => "Please set your data update");
			}

			## WHERE
			if (isset($this->where) && count($this->where) > 0)
			{
				foreach ($this->where as $key => $value)
				{
					$values[] = &$this->where[$key];
					if ($where == '')
					{
						$where .= ' WHERE ' . $key . '=?';
					} else {
						$where .= ' AND ' . $key . '=?';
					}
				}
			} else {
				return array("status" => "error", "result" => "Where condition cannot empty");
			}

			$sql 	= "UPDATE $table SET $updateCond $where";
			$this->sql = $sql;
			$stmt 	= sqlsrv_prepare($this->conn, $sql, $values);
			$row 	= sqlsrv_execute($stmt);
			if ($row)
			{
				$response = array(
					"status" => "success",
					"result" => "Data success update"
				);
			}
			else {
				$e = 'Data failed to update';
				if( ($errors = sqlsrv_errors() ) != null) {
			        foreach( $errors as $error ) {
			            $e .= '<br>'.$error[ 'message'];
			        }
			    }
				$response = array(
					"status" => "error",
					"result" => $e
				);
			}
		}
		##########################
		##        MYSQL			##
		##########################
		else {
			## UPDATE CONDITION
			if (count($params) > 0)
			{
				foreach ($params as $key => $val)
				{
					if ($val != "")
					{
						if ($updateCond == "")
						{
							$updateCond .= $key . "=:" . $key;
						} else {
							$updateCond .= "," . $key . "=:" . $key;
						}
					}
				}
			} else {
				return array("status" => "error", "result" => "Please set your data update");
			}

			
			## WHERE
			if (isset($this->where))
			{
				$i = 1;
				foreach ($this->where as $key => $value)
				{
					$fieldname = $key;

					if (strpos($key, '.'))
					{
						list($as, $key) = explode('.', $key);
						$fieldname = $as.'.'.$key;
					}

					## AND OR
					if (isset($this->condition[$i]) && $this->condition[$i] != NULL)
					{
						$conditionDes = $this->condition[$i];
					} else {
						$conditionDes = 'AND';
					}

					## OPERATOR =<>= in like
					if (isset($this->operator[$i]))
					{
						$operator = $this->operator[$i];
					} else {
						$operator = '=';
					}

					if ($where == '')
					{
						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							$where .= ' WHERE ' . $value;
						} else {
							$where .= ' WHERE ' . $fieldname . $operator . ':' . $key;
						}
					}
					else {
						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							$where .= ' '.$conditionDes.' ' . $value;
						} else {
							$where .= ' '.$conditionDes.' ' . $fieldname . $operator . ':' . $key;
						}
					}
					$i++;
				}
			} else {
				return array("status" => "error", "result" => "Where condition cannot empty");
			}

			try
			{
				$sql = "UPDATE $table SET $updateCond $where ;";
				$this->sql = $sql;
				$db = self::getConnection();
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$db->beginTransaction();
				$stmt = $db->prepare($sql);

				foreach ($params as $key =>& $val)
				{
					if ($val != "")
					{
						$stmt->bindValue(':'.$key, $val);
					}
				}

				if (isset($this->where))
				{
					$i = 1;
					foreach ($this->where as $key => $value)
					{
						$fieldname = $key;

						if (strpos($key, '.'))
						{
							list($as, $key) = explode('.', $key);
							$fieldname = $as.'.'.$key;
						}

						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							if (isset($this->value[$key]) && $this->value[$key] != '')
							{
								$bind = explode("||", $this->value[$key]);
								foreach ($bind as $k => $vals)
								{
									list($parameter, $resValue) = explode("=", $vals);
									$stmt->bindValue(':'.$parameter, $resValue);
								}
							}
						} else {
							if (is_string($value)) {
								$stmt->bindValue(':'.$key, $this->where[$fieldname], PDO::PARAM_STR);
							} else {
								$stmt->bindValue(':'.$key, $this->where[$fieldname]);
							}
						}

						$i++;
					}
				}

				$stmt->execute();
				$db->commit();
				
				$response = array(
					"status" => "success",
					"result" => "Data success update"
				);
			} catch(\PDOException $e) {
				$response = $response = array(
					"status" => "error",
					"result" => $e->getMessage()
				);
			}
		}
		return $response;
	}

	public function delete($table = '')
	{
		$where = '';

		## TABLE
		if ($table == '')
			return array("status" => "error", "result" => "Not table selection");

		##########################
		##     SQL SERVER		##
		##########################
		if (strtolower($this->dbType) == 'sqlserver')
		{
			## WHERE CONDITION
			$params = array();
			if (isset($this->where) && count($this->where) > 0)
			{
				foreach ($this->where as $key => $value)
				{
					$params[] = &$this->where[$key];
					if ($where == '')
					{
						$where = ' WHERE '.$key.'=?';
					} else {
						$where = ' ANDA '.$key.'=?';
					}
				}
			}
			else {
				return array("status" => "error", "result" => "Where condition cannot empty");
			}

			$sql 	= "DELETE FROM $table $where";
			$this->sql = $sql;
			$stmt 	= sqlsrv_prepare($this->conn, $sql, $params);
			$row 	= sqlsrv_execute($stmt);
			if ($row)
			{
				$response = array(
					"status" => "success",
					"result" => "Data success delete"
				);
			}
			else {
				$e = 'Data failed to delete !!';
				if( ($errors = sqlsrv_errors() ) != null) {
			        foreach( $errors as $error ) {
			            $e .= '<br>'.$error[ 'message'];
			        }
			    }
				$response = array(
					"status" => "error",
					"result" => $e
				);
			}
		}
		##########################
		##        MYSQL			##
		##########################
		else {
			## WHERE
			if (isset($this->where) && count($this->where) > 0)
			{
				foreach ($this->where as $key => $value)
				{
					$fieldname = $key;
					if ($where == '')
					{
						$where .= ' WHERE ' . $fieldname . '=' . ':' . $key;
					} else {
						$where .= ' AND ' . $fieldname . '=' . ':' . $key;
					}
				}
			} else {
				return array("status" => "error", "result" => "Where condition cannot empty");
			}

			try{
				$sql 	= "DELETE FROM $table $where";
				$this->sql = $sql;
				$db 	= self::getConnection();
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$stmt 	= $db->prepare($sql);
				
				if (isset($this->where))
				{
					foreach ($this->where as $key => $value)
					{
						$fieldname = $key;
						if (is_string($value)) {
							$stmt->bindValue(':'.$key, $this->where[$fieldname], PDO::PARAM_STR);
						} else {
							$stmt->bindValue(':'.$key, $this->where[$fieldname]);
						}
					}
				}

				$stmt->execute();

				$response = array(
					"status" => "success",
					"result" => "Data success delete"
				);
			} catch(\PDOException $e) {
				$response = $response = array(
					"status" => "error",
					"result" => $e->getMessage()
				);
			}
		}
		return $response;
	}

	## FOR COUNT
	public function count($table = '', $field = 'COUNT(*) AS total')
	{
		$where = '';
		$join  = '';

		## SELECTOR
		if (!isset($this->select))
			$this->select = '*';

		## FROM TABLE
		if (!isset($this->from) || $this->from == '')
		{
			if ($table != '')
			{
				$this->from = $table;
			} else {
				return array("status" => "error", "result" => "Not table selection");
			}
		}

		## JOIN
		if (isset($this->join) && $this->join != '')
		{
			foreach ($this->join as $key => $value)
			{
				$join .= ' LEFT JOIN '.$key.' ON '.$value.' ';
			}
		}

		##########################
		##     SQL SERVER		##
		##########################
		if (strtolower($this->dbType) == 'sqlserver')
		{
			$data = array();

			## WHERE
			if (isset($this->where))
			{
				foreach ($this->where as $key => $value)
				{
					$fieldname = $key;
					$data[] = &$this->where[$fieldname];

					if (strpos($key, '.'))
					{
						list($as, $key) = explode('.', $key);
						$fieldname = $as.'.'.$key;
					}

					if ($where == '')
					{
						$where .= ' WHERE ' . $fieldname . '=?';
					} else {
						$where .= ' AND ' . $fieldname . '=?';
					}
				}
			}

			$sql 	= "SELECT $field FROM $this->from $join $where";
			$this->sql = $sql;
			$stmt 	= sqlsrv_prepare($this->conn, $sql, $data);
			$row 	= sqlsrv_execute($stmt);
			if ($row)
			{
				$result = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC );
				
				if (!$result)
				{
					unset($result);
					$result = "0";
				}
				
				$response = array("status" => "success");
				$response += $result;
			}
			else {
				$e = 'Data failed to get';
				if( ($errors = sqlsrv_errors() ) != null) {
			        foreach( $errors as $error ) {
			            $e .= '<br>'.$error[ 'message'];
			        }
			    }
				$response = array(
					"status" => "error",
					"result" => $e
				);
			}
		}
		##########################
		##        MYSQL			##
		##########################
		else {
			## WHERE
			if (isset($this->where))
			{
				$i = 1;
				foreach ($this->where as $key => $value)
				{
					$fieldname = $key;

					if (strpos($key, '.'))
					{
						list($as, $key) = explode('.', $key);
						$fieldname = $as.'.'.$key;
					}

					## AND OR
					if (isset($this->condition[$i]) && $this->condition[$i] != NULL)
					{
						$conditionDes = $this->condition[$i];
					} else {
						$conditionDes = 'AND';
					}

					## OPERATOR =<>= in like
					if (isset($this->operator[$i]))
					{
						$operator = $this->operator[$i];
					} else {
						$operator = '=';
					}

					if ($where == '')
					{
						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							$where .= ' WHERE ' . $value;
						} else {
							$where .= ' WHERE ' . $fieldname . $operator . ':' . $key;
						}
					}
					else {
						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							$where .= ' '.$conditionDes.' ' . $value;
						} else {
							$where .= ' '.$conditionDes.' ' . $fieldname . $operator . ':' . $key;
						}
					}
					$i++;
				}
			}

			try {
				$db = self::getConnection();
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

				$sql = "SELECT $field FROM $this->from $join $where";
				$this->sql = $sql;
				$stmt = $db->prepare($sql);

				if (isset($this->where))
				{
					$i = 1;
					foreach ($this->where as $key => $value)
					{
						$fieldname = $key;

						if (strpos($key, '.'))
						{
							list($as, $key) = explode('.', $key);
							$fieldname = $as.'.'.$key;
						}

						if (isset($this->builder[$key]) && $this->builder[$key] === FALSE)
						{
							if (isset($this->value[$key]) && $this->value[$key] != '')
							{
								$bind = explode("||", $this->value[$key]);
								foreach ($bind as $k => $vals)
								{
									list($parameter, $resValue) = explode("=", $vals);
									$stmt->bindValue(':'.$parameter, $resValue);
								}
							}
						} else {
							if (is_string($value)) {
								$stmt->bindValue(':'.$key, $this->where[$fieldname], PDO::PARAM_STR);
							} else {
								$stmt->bindValue(':'.$key, $this->where[$fieldname]);
							}
						}

						$i++;
					}
				}

				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				if (!$result)
				{
					unset($result);
					$result = '0';
				}

				$response = array("status" => "success");
				$response += $result;

			} catch(\PDOException $e) {
				$response = $response = array(
					"status" => "error",
					"result" => $e->getMessage()
				);
			}
		}
		return $response;
	}

	## FOR QUERY
	public function query($sql = '', $dataWhere = array(), $fetchAll = TRUE)
	{
		##########################
		##     SQL SERVER		##
		##########################
		if (strtolower($this->dbType) == 'sqlserver')
		{
			## WHERE CONDITION
			$params = array();
			if (isset($this->where) && count($this->where) > 0)
			{
				foreach ($this->where as $key => $value)
				{
					$params[] = &$this->where[$key];
				}
			}
			else {
				if (count($dataWhere) > 0)
				{
					foreach ($dataWhere as $key => $value)
					{
						$params[] = &$dataWhere[$key];
					}
				}
			}

			$stmt 	= sqlsrv_prepare($this->conn, $sql, $params);
			$this->sql = $sql;
			$row 	= sqlsrv_execute($stmt);
			if ($row)
			{
				$result = 'Success run query';
				if ($fetchAll && $this->allRow)
				{
					unset($result);
					while( $resultTemp = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC ))
					{
						$result[] = $resultTemp;
					}
				}
				else {
					unset($result);
					$result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
				}

				$response = array(
					"status" => "success",
					"result" => $result
				);
			} else {
				$e = 'Please check your query  !!';
				if( ($errors = sqlsrv_errors() ) != null) {
			        foreach( $errors as $error ) {
			            $e .= '<br>'.$error[ 'message'];
			        }
			    }
				$response = array(
					"status" => "error",
					"result" => $e
				);
			}
		}
		##########################
		##        MYSQL			##
		##########################
		else {
			$where = '';

			## FROM TABLE
			if ($sql == '')
				return array("status" => "error", "result" => "Cannot empty query");

			try {
				$db = self::getConnection();
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

				$stmt = $db->prepare($sql);
				$this->sql = $sql;

				if (isset($this->where) && count($this->where) > 0)
				{
					foreach ($this->where as $key => $value)
					{
						if (is_string($value)) {
							$stmt->bindValue(':'.$key, $this->where[$key], PDO::PARAM_STR);
						} else {
							$stmt->bindValue(':'.$key, $this->where[$key]);
						}
					}
				} else {
					if (count($dataWhere) > 0)
					{
						foreach ($dataWhere as $key => $value)
						{
							if (is_string($value)) {
								$stmt->bindValue(':'.$key, $dataWhere[$key], PDO::PARAM_STR);
							} else {
								$stmt->bindValue(':'.$key, $dataWhere[$key]);
							}
						}
					}
				}

				$stmt->execute();
				if ($fetchAll && $this->allRow)
					$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				else
					$result = $stmt->fetch(PDO::FETCH_ASSOC);

				if (!$result) {
					unset($result);
					$result = 'Success run query';
				}

				$response = array(
					"status" => "success",
					"result" => $result
				);

			} catch(\PDOException $e) {
				$response = $response = array(
					"status" => "error",
					"result" => $e->getMessage()
				);
			}
		}
		return $response;
	}
}