<?php namespace system;

use system\loader;
use system\libraries\input;
use system\libraries\file;
use system\libraries\httpLib;
use system\libraries\globalFunction;
use system\libraries\generatorUniqueCode;
use GeoIp2\Database\Reader;

abstract class BaseController
{
    protected $params;

    public function __construct($params)
    {
        require_once APP_PATH . '/vendor/autoload.php';
        
        /* LOAD TWIG */
        require_once APP_PATH . '/vendor/twig/lib/Twig/Autoloader.php';
        $autoloader     = new \Twig_Autoloader();
        $autoloader::register();
        // Twig_Autoloader::register();
        /* PATH FILE HTML */
        $this->loader   = new \Twig_Loader_Filesystem(VIEW_PATH);
        /* CACHE FILE
        $twig = new Twig_Environment($loader, array( 'cache' => 'cache')); */
        $this->twig     = new \Twig_Environment($this->loader);
        $this->view     = $this->twig;

        /* GET CONFIG FILE */
        global $config;
        $this->config   = $config;

        /* FROM LIBRARIES */
        $this->load     = new loader();
        $this->input    = new input();
        $this->file     = new file();
        $this->http     = new httpLib();
        $this->app      = new globalFunction();
        $this->generateUniqueCode = new generatorUniqueCode();
        $this->reader = new Reader(BASE_PATH."/geoip/GeoLite2-City.mmdb");

        $this->action   = $params['action'];
        $this->param    = $params['params'];
    }

    public function ExecuteAction()
    {
        if ($this->param)
            return call_user_func_array(array($this, $this->action), $this->param);
        else
            return $this->{$this->action}();
    }

    protected function View($view, $data, $template = true)
    {
        $classData = explode("\\", get_class($this));
        $className = end($classData);

        $content = PUBLIC_PATH . "/views/" . $view . ".php";

        if ($template)
        {
            require PUBLIC_PATH . "/views/template.php";
        } else {
            require $content;
        }
    }

    public static function &getInstance()
    {
        return self::$instance;
    }
}