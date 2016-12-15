<?php namespace config;

abstract class Database
{
    public static function getConnection()
    {
    	global $config;

		##########################
		##     SQL SERVER		##
		##########################
        if (isset($config['database']['connection']) && strtolower($config['database']['connection']) == 'sqlserver')
        {
			$serverName = "USER\SQLEXPRESS"; //serverName\instanceName
			$connectionInfo['Database'] = 'databasename';
			$connectionInfo['UID'] = 'user';
			$connectionInfo['PWD'] = 'password';
			return array('serverName' => $serverName, 'connectionInfo' => $connectionInfo);
    	}
		##########################
		##        MYSQL			##
		##########################
    	else {
	        try {
			    $dbhost	= "127.0.0.1";
			    $dbuser	= "root";
			    $dbpass	= "password";
			    $dbname	= "databasename";
		        return new \PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			}
			catch (\PDOException $e) {
			    die("Failed to get DB handle: " . $e->getMessage());
			}
        }
    }
}
?>