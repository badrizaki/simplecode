<?php namespace system;

/**
  *  Name         : Simplecode Loader.
  *  Description  : This class for loader.
  *  Featured     : Load Model, Load Controller, Load Config, Load Helper
  *  @copyright   : Badri Zaki
  *  @version     : 1.0
  *  @author      : Badri Zaki - badrizaki@gmail.com
**/

use system\Router;

class loader
{
    ## For load model
    public function model($modelName, $params = NULL)
    {
        $modelFile = APP_PATH . '/models/'.$modelName . ".php";
        if (file_exists($modelFile)) 
            require_once $modelFile;
        
        return new $modelName($params);
    }

    ## For load file in folder config
    public function config($configName)
    {
        $configFile = APP_PATH . '/config/'.$configName . ".php";
        if (file_exists($configFile))
            include $configFile;

        $this->config = $config;
        return $config;
    }

    ## For load controller
    public function controller($controllerName, $type='', $action = 'index', $params = NULL)
    {
        $dir = '';
        if ($type != '')
        {
            $dir = $type."/";
            $controller = "$type\\".$controllerName;
        } else {
            $controller = $controllerName;
        }

        /* SET TO PARAM */
        $urlParams = array("controller" => $controller, "action" => $action);

        /* GET ROUTER CLASS */
        $router     = new Router($urlParams);
        $controller = $router->getController();

        /* CALL CONTROLLER FOR EXECUTION */
        $controller->ExecuteAction();
    }

    ## For load file in folder helper
    public function helper($helperName)
    {
        $helperFile = APP_PATH . '/helpers/'.$helperName . ".php";
        if (file_exists($helperFile))
            include $helperFile;

        $this->helper = $helper;
        return $helper;
    }

    ## For load lib
    public function lib($libName, $params = NULL)
    {
        $libFile = APP_PATH . '/libs/'.$libName . ".php";
        if (file_exists($libFile))
            include_once($libFile);

        return new $libName($params);
    }
}