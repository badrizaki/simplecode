<?php

/**
  *  Name         : Simplecode App.
  *  Description  : This class for Application get controller and vendor.
  *  Featured     : Method -> get, post and notFound
  *  @copyright   : Atom Media Studio
  *  @version     : 1.8
  *  @author      : Badri Zaki - badrizaki@atommediastudio.com
**/

class Application
{
    public function get($url = '', $controllers = '', $function = '', $paramFunc = array())
    {
		if ($_SERVER['REQUEST_METHOD'] == "GET")
		{
			$pass = false;

			if (isset($_GET['controller']))
				$_GET['controller'] = $_GET['controller'];
			else {
				$path_def 			= str_replace('index.php', '', $_SERVER['PHP_SELF']);
				$REDIRECT_URL 		= isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL']:'';
				$_GET['controller'] = ($path_def != '/') ? str_replace($path_def, '', $REDIRECT_URL):'';
				if (isset($_GET['/index_php'])) unset($_GET['/index_php']);
			}

			$_GET['controller'] = trim(str_replace('public/', '', $_GET['controller']), '/');

			$urlArr 		 	= explode('/', $url);
			$countUrl 		 	= count($urlArr);

			$paramArr 		 	= explode('/', $_GET['controller']);
			$countParam 	 	= count($paramArr);

			$params 		 	= array();
	    	$countUrlBracket 	= preg_match_all('#\((.*?)\)#', $url, $match);
			if ($countUrl == $countParam && $countUrlBracket >= 1)
			{
		    	foreach ($urlArr as $key => $value)
		    	{
		    		## IF BRACKET (:VALUE) IS EXISTS
		    		if (preg_match('#\((.*?)\)#', $value, $match))
		    		{
		    			$pass 		= true;
		    			$params[] 	= $paramArr[$key];
		    		}
		    		else {
		    			if ($urlArr[$key] != $paramArr[$key])
		    			{
		    				$pass 	= false;
		    				break;
		    			}
		    		}
				}
	    	}

	    	if ($pass || $url == $_GET['controller'] || $url."/" == $_GET['controller'])
	    	{
	    		global $result;
	    		if ($function != '') call_user_func_array($function, array($paramFunc));

	    		/* EXPLODE FOR CLASS AND FUNCTION */
	    		list($class, $function) = explode(":", $controllers);
	    		
	    		/* SET TO PARAM */
	    		$urlParams 	= array("controller" => $class, "action" => $function, "params" => $params);

	    		/* GET ROUTER CLASS */
			    $router 	= new system\Router($urlParams);
			    $controller = $router->getController();
			    
			    /* REMOVE PARAMETER CONTROLLER AND ACTION */
			    unset($_GET['controller']);
			    unset($_GET['action']);

			    /* CALL CONTROLLER FOR EXECUTION */
			    $controller->ExecuteAction();
			    
			    /* SET RESULT VALUE FOR NOT FOUND PAGE */
			    $result = true;die;
	    	}
		}
    }

    public function post($url='', $controllers ='', $function = '', $paramFunc = array())
    {
		if ($_SERVER['REQUEST_METHOD'] == "POST")
		{
			$pass = false;

			if (isset($_GET['controller']))
				$_GET['controller'] = $_GET['controller'];
			else {
				$path_def 			= str_replace('index.php', '', $_SERVER['PHP_SELF']);
				$REDIRECT_URL 		= isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL']:'';
				$_GET['controller'] = ($path_def != '/') ? str_replace($path_def, '', $REDIRECT_URL):'';
			}

			$_GET['controller'] = trim(str_replace('public/', '', $_GET['controller']), '/');

			$urlArr 			= explode('/', $url);
			$countUrl 			= count($urlArr);

			$paramArr 			= explode('/', $_GET['controller']);
			$countParam 		= count($paramArr);

			$params 			= array();
	    	$countUrlBracket 	= preg_match_all('#\((.*?)\)#', $url, $match);
			if ($countUrl == $countParam && $countUrlBracket >= 1)
			{
		    	foreach ($urlArr as $key => $value)
		    	{
		    		## IF BRACKET (:VALUE) IS EXISTS
		    		if (preg_match('#\((.*?)\)#', $value, $match))
		    		{
		    			$pass 		= true;
		    			$params[] 	= $paramArr[$key];
		    		}
		    		else {
		    			if ($urlArr[$key] != $paramArr[$key])
		    			{
		    				$pass 	= false;
		    				break;
		    			}
		    		}
				}
	    	}

	    	if ($pass || $url == $_GET['controller'] || $url."/" == $_GET['controller'])
	    	{
	    		global $result;
	    		if ($function != '') call_user_func_array($function, array($paramFunc));
	    		
	    		/* EXPLODE FOR CLASS AND FUNCTION */
	    		list($class, $function) = explode(":", $controllers);
	    		
	    		/* SET TO PARAM */
	    		$urlParams 	= array("controller" => $class, "action" => $function, "params" => $params);

	    		/* GET ROUTER CLASS */
			    $router 	= new system\Router($urlParams);
			    $controller = $router->getController();
			    
			    /* REMOVE PARAMETER CONTROLLER AND ACTION */
			    unset($_GET['controller']);
			    unset($_GET['action']);

			    /* CALL CONTROLLER FOR EXECUTION */
			    $controller->ExecuteAction();
			    
			    /* SET RESULT VALUE FOR NOT FOUND PAGE */
			    $result = true;die;
	    	}
		}
    }

    public function NotFound()
    {
    	/* SET */
    	$urlParams 	= array("controller" => 'errors\NotFound', "action" => 'index', "params" => '');

		/* GET ROUTER CLASS */
	    $router 	= new system\Router($urlParams);
	    $controller = $router->getController();
	    
	    /* REMOVE PARAMETER CONTROLLER AND ACTION */
	    unset($_GET['controller']);
	    unset($_GET['action']);

	    /* CALL CONTROLLER FOR EXECUTION */
	    $controller->ExecuteAction();
    }

    function redirect($url = '')
    {
    	header('Location: ' . $url);die;
    }
}