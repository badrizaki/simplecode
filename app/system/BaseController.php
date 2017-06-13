<?php namespace system;

use stdClass;
use system\loader;
use system\libraries\input;
use system\libraries\file;
use system\libraries\httpLib;
use system\libraries\globalFunction;
use system\libraries\generatorUniqueCode;
use system\libraries\validation;
use system\libraries\visitor;
use system\libraries\ErrorPage;
use system\libraries\Messages;
use system\libraries\Response;
use system\libraries\StatusExecution;

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
        // $this->view     = $this->twig;

        /* GET CONFIG FILE */
        global $config;
        $this->config   = $config;

        /* FROM LIBRARIES */
        $this->errorPage    = new ErrorPage();
        $this->load         = new loader();
        $this->response     = new Response();
        $this->libraries();

        $this->action   = $params['action'];
        $this->param    = $params['params'];
    }

    public function libraries()
    {
        $this->lib = new stdClass();
        $this->lib->input    = new input();
        $this->lib->file     = new file();
        $this->lib->http     = new httpLib();
        $this->lib->global   = new globalFunction();
        $this->lib->generateUniqueCode  = new generatorUniqueCode();
        $this->lib->validation          = new validation();
        $this->lib->visitor  = new visitor();
        $this->lib->flash    = new Messages();
        $this->lib->statusExecution    = new StatusExecution();
    }

    public function ExecuteAction()
    {
        if ($this->param)
            return call_user_func_array(array($this, $this->action), $this->param);
        else
            return $this->{$this->action}();
    }

    public function view($view = '', $data = array())
    {
        echo $this->twig->render($view, $data);
    }

    protected function view_def($view, $data, $template = true)
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